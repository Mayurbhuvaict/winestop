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



namespace Mirasvit\Seo\Observer\System\Cms;

use Magento\Framework\Event\ObserverInterface;

class PrepareFormObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->registry = $registry;
        $this->request = $request;

    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function prepareForm($observer)
    {
        $model = $this->registry->registry('cms_page');
        $form = $observer->getForm();
        $fieldset = $form->addFieldset(
            'seo_alternate_fieldset',
            ['legend' => __('Alternate Settings'), 'class' => 'input-text']
        );
        $fieldset->addField('alternate_group', 'text', [
            'name' => 'alternate_group',
            'label' => __('Alternate group'),
            'title' => __('Alternate group'),
            'disabled' => false,
            'value' => $model->getAlternateGroup(),
        ]);

        $fieldset = $form->addFieldset(
            'open_graph_image_url_fieldset',
            ['legend' => __('Open Graph Image Url'), 'class' => 'input-text']
        );
        $fieldset->addField('open_graph_url', 'text', [
            'name' => 'open_graph_image_url',
            'label' => __('Open Graph Image Url'),
            'title' => __('Open Graph Image Url'),
            'disabled' => false,
            'value' => $model->getOpenGraphImageUrl(),
        ]);
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * bannerslideradmin_banner_edit - action of Plazathemes Banner Slider.
         * When action was not excluded, the visits of "Create New Banner" page fails
         * with "$model->getAlternateGroup() on null error"
         * ticket id: 2398234
         */
        if(strpos($this->request->getFullActionName(), 'bannerslideradmin') === false) {
            $this->prepareForm($observer);
        }
    }
}
