<?php

/**
 * @category    ScandiPWA
 * @package     ScandiPWA_ContactGraphQl
 * @copyright   Copyright © Scandiweb, Inc. All rights reserved.
 * @copyright   Modifications © Selveq. All rights reserved.
 * @license     OSL-3.0 (Open Software License ("OSL") v. 3.0)
 * See LICENSE for license details.
 */

namespace ScandiPWA\ContactGraphQl\Setup\Patch\Data;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\GetBlockByIdentifierInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\Store;

class CreateContactUsBlock implements DataPatchInterface
{
    public const string BLOCK_IDENTIFIER = 'contact_us_page_block';
    public const string CONFIG_PATH = 'content_customization/contact_us_content/contact_us_cms_block';

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param BlockFactory $blockFactory
     * @param BlockRepositoryInterface $blockRepository
     * @param GetBlockByIdentifierInterface $getBlockByIdentifier
     * @param ConfigResource $configResource
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly BlockFactory $blockFactory,
        private readonly BlockRepositoryInterface $blockRepository,
        private readonly GetBlockByIdentifierInterface $getBlockByIdentifier,
        private readonly ConfigResource $configResource,
        private readonly ScopeConfigInterface $scopeConfig
    ) {}

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        // store 0 skips the cms_block_store join, so this finds the block whatever store view it is assigned to
        try {
            $this->getBlockByIdentifier->execute(self::BLOCK_IDENTIFIER, Store::DEFAULT_STORE_ID);
        } catch (NoSuchEntityException) {
            $block = $this->blockFactory->create()->setData($this->getBlockData());

            $this->blockRepository->save($block);
        }

        // the admin owns this field, so a re-application must not overwrite a block they chose themselves
        if ($this->scopeConfig->getValue(self::CONFIG_PATH) === null) {
            $this->configResource->saveConfig(self::CONFIG_PATH, self::BLOCK_IDENTIFIER);
        }

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return array
     */
    private function getBlockData(): array
    {
        return [
            'title' => 'Contact Us Page Block',
            'identifier' => self::BLOCK_IDENTIFIER,
            'content' => '<div class="contact-info cms-content">'
                . '<p>Use the form on this page to get in touch with us and we will reply as soon as we can.</p>'
                . '</div>',
            'is_active' => 1,
            'stores' => [Store::DEFAULT_STORE_ID]
        ];
    }
}
