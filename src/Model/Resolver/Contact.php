<?php

/**
 * @category    ScandiPWA
 * @package     ScandiPWA_ContactGraphQl
 * @copyright   Copyright 2014 Adobe. All Rights Reserved.
 * @copyright   Copyright © Scandiweb, Inc. All rights reserved.
 * @copyright   Modifications © Selveq. All rights reserved.
 * @license     OSL-3.0 (Open Software License ("OSL") v. 3.0)
 * See LICENSE for license details.
 */

namespace ScandiPWA\ContactGraphQl\Model\Resolver;

use Exception;
use Magento\Contact\Model\ConfigInterface;
use Magento\Contact\Model\MailInterface;
use Magento\Framework\DataObject;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Psr\Log\LoggerInterface;

class Contact implements ResolverInterface
{
    // every field is mailed verbatim, so an unbounded field is an unbounded email
    private const array FIELD_LIMITS = [
        'name' => 255,
        'telephone' => 64,
        'email' => 254,
        'message' => 10000
    ];

    private const string SIZE_GUARD = 'contact_form_size';

    /**
     * @param MailInterface $mail
     * @param ConfigInterface $mailConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly MailInterface $mail,
        private readonly ConfigInterface $mailConfig,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * {@inheritdoc}
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        if (!$this->mailConfig->isEnabled()) {
            throw new GraphQlInputException(__('Contact is disabled'));
        }

        $contact = $args['contact'] ?? [];
        $this->validateSize($contact);

        // the mail template reads comment, the theme sends message
        $data = [
            'name' => $contact['name'] ?? '',
            'telephone' => $contact['telephone'] ?? '',
            'email' => $contact['email'] ?? '',
            'comment' => $contact['message'] ?? ''
        ];
        $this->validateParams($data);

        try {
            $this->mail->send(
                $data['email'],
                ['data' => new DataObject($data)]
            );
        } catch (Exception $e) {
            $this->logger->critical($e);

            throw new GraphQlInputException(
                __('An error occurred while processing your form. Please try again later.')
            );
        }

        return ['message' => __('Your message has been sent and we will contact you ASAP')];
    }

    /**
     * @param array $contact
     * @return void
     * @throws GraphQlInputException
     */
    private function validateSize(array $contact): void
    {
        foreach (self::FIELD_LIMITS as $name => $limit) {
            $length = mb_strlen(trim((string)($contact[$name] ?? '')));
            if ($length <= $limit) {
                continue;
            }

            $this->logger->warning(sprintf('%s: %s is %d characters', self::SIZE_GUARD, $name, $length));

            throw new GraphQlInputException(
                __('The %1 is too long. Enter at most %2 characters and try again.', $name, $limit)
            );
        }
    }

    /**
     * @param array $data
     * @return void
     * @throws GraphQlInputException
     */
    private function validateParams(array $data): void
    {
        if (trim($data['name']) === '') {
            throw new GraphQlInputException(__('Enter the Name and try again.'));
        }

        if (trim($data['comment']) === '') {
            throw new GraphQlInputException(__('Enter the comment and try again.'));
        }

        if (false === filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new GraphQlInputException(
                __('The email address is invalid. Verify the email address and try again.')
            );
        }
    }
}
