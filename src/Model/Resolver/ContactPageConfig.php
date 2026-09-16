<?php

/**
 * @category    ScandiPWA
 * @package     ScandiPWA_ContactGraphQl
 * @copyright   Copyright © Scandiweb, Inc. All rights reserved.
 * @copyright   Modifications © Selveq. All rights reserved.
 * @license     OSL-3.0 (Open Software License ("OSL") v. 3.0)
 * See LICENSE for license details.
 */

namespace ScandiPWA\ContactGraphQl\Model\Resolver;

use Magento\Contact\Model\ConfigInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ContactPageConfig implements ResolverInterface
{
    /**
     * @param ConfigInterface $mailConfig
     */
    public function __construct(
        private readonly ConfigInterface $mailConfig
    ) {}

    /**
     * {@inheritdoc}
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        return [
            'enabled' => $this->mailConfig->isEnabled()
        ];
    }
}
