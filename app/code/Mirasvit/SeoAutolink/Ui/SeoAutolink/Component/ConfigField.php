<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\SeoAutolink\Ui\SeoAutolink\Component;

use Magento\Ui\Component\Form\Field;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class ConfigField extends Field
{
    /**
     * @var StoreCheck
     */
    private $storeCheck;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param StoreCheck $storeCheck
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreCheck $storeCheck,
        array $components = [],
        array $data = []
    ) {
        $this->storeCheck =  $storeCheck;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, $value = null)
    {
        if ($key == 'config'
            && isset($value['dataScope'])
            && $value['dataScope'] == 'use_config.store_ids'
            && $this->storeCheck->isAppliedAllStores()) {
                $value['value'] = true;
        }

        parent::setData($key, $value);
    }
}
