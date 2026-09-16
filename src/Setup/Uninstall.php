<?php

/**
 * @category    ScandiPWA
 * @package     ScandiPWA_ContactGraphQl
 * @copyright   Copyright © Scandiweb, Inc. All rights reserved.
 * @copyright   Modifications © Selveq. All rights reserved.
 * @license     OSL-3.0 (Open Software License ("OSL") v. 3.0)
 * See LICENSE for license details.
 */

namespace ScandiPWA\ContactGraphQl\Setup;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use ScandiPWA\ContactGraphQl\Setup\Patch\Data\CreateContactUsBlock;

class Uninstall implements UninstallInterface
{
    /**
     * @param BlockRepositoryInterface $blockRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Config $configResource
     */
    public function __construct(
        private readonly BlockRepositoryInterface $blockRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly Config $configResource
    ) {}

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('identifier', CreateContactUsBlock::BLOCK_IDENTIFIER)
            ->create();

        foreach ($this->blockRepository->getList($searchCriteria)->getItems() as $block) {
            $this->blockRepository->delete($block);
        }

        $this->configResource->deleteConfig(CreateContactUsBlock::CONFIG_PATH);

        $setup->endSetup();
    }
}
