<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    AdvancedReports
 * @version    2.8.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
















































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Toolbar;

use Aheadworks\AdvancedReports\Ui\Component\OptionsContainer;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\AdvancedReports\Model\Filter\Store as StoreFilter;
use Aheadworks\AdvancedReports\Model\Filter\Store\Encoder as FilterStoreEncoder;

/**
 * Class Store
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Toolbar
 */
class Store extends OptionsContainer
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var FilterStoreEncoder
     */
    private $filterStoreEncoder;

    /**
     * @param ContextInterface $context
     * @param StoreManagerInterface $storeManager
     * @param FilterStoreEncoder $filterStoreEncoder
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        StoreManagerInterface $storeManager,
        FilterStoreEncoder $filterStoreEncoder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->storeManager = $storeManager;
        $this->filterStoreEncoder = $filterStoreEncoder;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        $storeFilter = $this->context->getDataProvider()->getDefaultFilterPool()->getFilter('store');
        $this->addOptions();
        $config = $this->getData('config');

        $config['currentValue'] = $storeFilter->getValue();

        $this->setData('config', $config);
    }

    /**
     * Add options
     *
     * @return $this
     */
    private function addOptions()
    {
        $config = $this->getData('config');
        $config['options'] = $this->getStoreOptions();
        $this->setData('config', $config);

        return $this;
    }

    /**
     * Retrieve store options
     *
     * @return array
     */
    private function getStoreOptions()
    {
        $options[] = [
            'value' => $this->filterStoreEncoder->encode(StoreFilter::DEFAULT_TYPE, StoreFilter::DEFAULT_TYPE),
            'label' => __('All Store Views')
        ];
        foreach ($this->storeManager->getWebsites() as $website) {
            $options[] = [
                'value' => $this->filterStoreEncoder->encode(StoreFilter::WEBSITE_TYPE, $website->getId()),
                'label' => $website->getName()
            ];
            foreach ($website->getGroups() as $group) {
                $options[] = [
                    'additionalClasses' => ['menu-inner-level-1' => true],
                    'value' => $this->filterStoreEncoder->encode(StoreFilter::GROUP_TYPE, $group->getId()),
                    'label' => $group->getName()
                ];
                foreach ($group->getStores() as $store) {
                    $options[] = [
                        'additionalClasses' => ['menu-inner-level-2' => true],
                        'value' => $this->filterStoreEncoder->encode(StoreFilter::STORE_TYPE, $store->getId()),
                        'label' => $store->getName()
                    ];
                }
            }
        }

        return $options;
    }
}
