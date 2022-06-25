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


declare(strict_types=1);


namespace Mirasvit\SeoContent\Block\Adminhtml\Template;


use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoContent\Api\Repository\TemplateRepositoryInterface;

class Edit extends Container
{
    private $registry;

    private $templateRepository;

    public function __construct(
        TemplateRepositoryInterface $templateRepository,
        Registry $registry,
        Context $context
    ) {
        $this->registry           = $registry;
        $this->templateRepository = $templateRepository;

        parent::__construct($context);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_objectId   = 'template_id';
        $this->_blockGroup = 'Mirasvit_SeoContent';
        $this->_controller = 'adminhtml_template';

        $this->buttonList->remove('save');
        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');

        if ($id = $this->getRequest()->getParam($this->_objectId)) {
            $previewUrl = $this->getUrl('*/*/preview', [$this->_objectId => $id]);
        } else {
            $previewUrl = $this->getUrl('*/*/preview', [$this->_objectId => 'new']);
        }


        $this->buttonList->add('preview', [
            'label'          => __('Preview Template'),
            'class'          => 'preview',
            'data_attribute' => [
                'mage-init' => [
                    'Mirasvit_SeoContent/js/template/form/preview' => [
                        'url' => $previewUrl
                    ],
                ]
            ]
        ], -100);
    }
}
