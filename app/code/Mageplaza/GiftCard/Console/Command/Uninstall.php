<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCard
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCard\Console\Command;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\ModuleResource;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\GiftCard\Model\Product\Type\GiftCard;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Reset
 * @package Mageplaza\Security\Console\Command
 */
class Uninstall extends Command
{
    /**
     * @var ModuleResource
     */
    protected $moduleResource;

    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    protected $eavAttrCodes = [
        'gift_code_pattern',
        'gift_card_type',
        'gift_card_amounts',
        'allow_amount_range',
        'min_amount',
        'max_amount',
        'price_rate',
        'can_redeem',
        'expire_after_day',
        'gift_product_template',
    ];

    /**
     * Uninstall constructor.
     *
     * @param ModuleResource $moduleResource
     * @param AttributeFactory $attributeFactory
     * @param CollectionFactory $collectionFactory
     * @param State $state
     * @param StoreManagerInterface $storeManager
     * @param null $name
     */
    public function __construct(
        ModuleResource $moduleResource,
        AttributeFactory $attributeFactory,
        CollectionFactory $collectionFactory,
        State $state,
        StoreManagerInterface $storeManager,
        $name = null
    ) {
        $this->moduleResource    = $moduleResource;
        $this->attributeFactory  = $attributeFactory;
        $this->collectionFactory = $collectionFactory;
        $this->state             = $state;
        $this->storeManager      = $storeManager;

        parent::__construct($name);
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('giftcard:uninstall')->setDescription('Prepare for remove Mageplaza_GiftCard module');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws LocalizedException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode('adminhtml');
        try {
            $productCollection = $this->collectionFactory->create()
                ->addFieldToFilter('type_id', GiftCard::TYPE_GIFTCARD);
            /** @var Product $product */
            foreach ($productCollection as $product) {
                $stores = $this->storeManager->getStores();
                foreach ($stores as $store) {
                    $product->addAttributeUpdate('visibility', 1, $store->getId());
                }
            }
            $this->attributeFactory->create()->getCollection()->addFieldToFilter('entity_type_id', 4)
                ->addFieldToFilter('attribute_code', ['in' => $this->eavAttrCodes])->walk('delete');
            $this->moduleResource->getConnection()
                ->delete($this->moduleResource->getMainTable(), "module='Mageplaza_GiftCard'");
            $output->writeln('<info>Prepare remove Mageplaza_GiftCard module successfully</info>');
        } catch (Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
    }
}
