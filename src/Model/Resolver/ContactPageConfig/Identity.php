<?php

/**
 * @category    ScandiPWA
 * @package     ScandiPWA_ContactGraphQl
 * @copyright   Copyright © Selveq. All rights reserved.
 * @license     OSL-3.0 (Open Software License ("OSL") v. 3.0)
 * See LICENSE for license details.
 */

declare(strict_types=1);

namespace ScandiPWA\ContactGraphQl\Model\Resolver\ContactPageConfig;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\StoreGraphQl\Model\Resolver\Store\ConfigIdentity;

class Identity implements IdentityInterface
{
    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        private readonly StoreManagerInterface $storeManager
    ) {}

    /**
     * {@inheritdoc}
     * @throws NoSuchEntityException
     */
    public function getIdentities(array $resolvedData): array
    {
        // the answer is store configuration, so it is invalidated by the tag ConfigTagGenerator cleans on every save
        $storeId = $this->storeManager->getStore()->getId();

        return [ConfigIdentity::CACHE_TAG, sprintf('%s_%s', ConfigIdentity::CACHE_TAG, $storeId)];
    }
}
