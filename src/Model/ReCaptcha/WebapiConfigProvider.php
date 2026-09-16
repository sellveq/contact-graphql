<?php

/**
 * @category    ScandiPWA
 * @package     ScandiPWA_ContactGraphQl
 * @copyright   Copyright 2025 Adobe. All Rights Reserved.
 * @copyright   Modifications © Selveq. All rights reserved.
 * @license     OSL-3.0 (Open Software License ("OSL") v. 3.0)
 * See LICENSE for license details.
 */

namespace ScandiPWA\ContactGraphQl\Model\ReCaptcha;

use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaWebapiApi\Api\Data\EndpointInterface;
use Magento\ReCaptchaWebapiApi\Api\WebapiValidationConfigProviderInterface;
use ScandiPWA\ContactGraphQl\Model\Resolver\Contact;

class WebapiConfigProvider implements WebapiValidationConfigProviderInterface
{
    // the admin's Contact Us setting is stored under this id, so reusing it is what makes that toggle cover contactForm
    private const string CAPTCHA_ID = 'contact';

    /**
     * @param IsCaptchaEnabledInterface $isEnabled
     * @param ValidationConfigResolverInterface $configResolver
     */
    public function __construct(
        private readonly IsCaptchaEnabledInterface $isEnabled,
        private readonly ValidationConfigResolverInterface $configResolver
    ) {}

    /**
     * {@inheritdoc}
     */
    public function getConfigFor(EndpointInterface $endpoint): ?ValidationConfigInterface
    {
        if ($endpoint->getServiceMethod() === 'resolve'
            && $endpoint->getServiceClass() === Contact::class
            && $this->isEnabled->isCaptchaEnabledFor(self::CAPTCHA_ID)
        ) {
            return $this->configResolver->get(self::CAPTCHA_ID);
        }

        return null;
    }
}
