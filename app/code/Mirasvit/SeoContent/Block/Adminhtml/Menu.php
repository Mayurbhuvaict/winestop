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



namespace Mirasvit\SeoContent\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Block\Adminhtml\AbstractMenu;

class Menu extends AbstractMenu
{
    /**
     * Menu constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->visibleAt(['seo_content']);

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildMenu()
    {
        $this->addItem([
            'resource' => 'Mirasvit_SeoContent::seo_content_template',
            'title'    => __('Template Manager'),
            'url'      => $this->urlBuilder->getUrl('seo_content/template'),
        ])->addItem([
            'resource' => 'Mirasvit_SeoContent::seo_content_rewrite',
            'title'    => __('Rewrite Manager'),
            'url'      => $this->urlBuilder->getUrl('seo_content/rewrite'),
        ])->addItem([
            'resource' => 'Mirasvit_SeoContent::config_seo',
            'title'    => __('Settings'),
            'url'      => $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/seo'),
        ]);
        ;

        return $this;
    }
}
