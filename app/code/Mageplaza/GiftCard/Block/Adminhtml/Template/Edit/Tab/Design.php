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

namespace Mageplaza\GiftCard\Block\Adminhtml\Template\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Helper\Template;
use Mageplaza\GiftCard\Model\Source\Fonts;
use Mageplaza\GiftCard\Model\TemplateFactory;

/**
 * Class Design
 * @package Mageplaza\GiftCard\Block\Adminhtml\Template\Edit\Tab
 */
class Design extends Generic implements TabInterface
{
    /**
     * @var string
     */
    protected $_template = 'template/design.phtml';

    /**
     * @var Template
     */
    protected $_templateHelper;

    /**
     * @var TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var Fonts
     */
    protected $fonts;

    /**
     * Design constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TemplateFactory $templateFactory
     * @param Template $templateHelper
     * @param Fonts $fonts
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TemplateFactory $templateFactory,
        Template $templateHelper,
        Fonts $fonts,
        array $data = []
    ) {
        $this->_templateFactory = $templateFactory;
        $this->_templateHelper  = $templateHelper;
        $this->fonts            = $fonts;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return mixed
     */
    public function getDataObject()
    {
        return $this->_coreRegistry->registry('current_template');
    }

    /**
     * @return array
     */
    public function getExistTemplates()
    {
        $existTemplate = [];
        $templates     = $this->_templateFactory->create()->getCollection();
        $design        = $this->getDataObject();
        if ($design->getId()) {
            $templates->addFieldToFilter('template_id', ['neq' => $design->getId()]);
        }

        foreach ($templates as $template) {
            if ($template->getDesign()) {
                $existTemplate[$template->getId()] = [
                    'name'   => $this->escapeQuote($template->getName()),
                    'design' => $template->getDesign()
                ];
            }
        }

        return $existTemplate;
    }

    /**
     * @return array|mixed
     */
    public function getDesignFields()
    {
        $design = $this->getDataObject()->getDesign();

        $designFields = [
            'giftcard' => [
                'width'  => 500,
                'height' => 300
            ]
        ];

        if (is_string($design) && $design) {
            $designFields = Data::jsonDecode($design);
        }

        return $designFields;
    }

    /**
     * @return array
     */
    public function getInitDesignFields()
    {
        $fields = $this->_templateHelper->getTemplateFields();
        foreach ($fields as &$field) {
            if (isset($field['css'])) {
                $field['css'] = array_merge($field['css'], ['padding' => '5px']);
            }
        }

        return $fields;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Design');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Design');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return bool|string
     */
    public function getFonts()
    {
        $fonts  = array_column($this->fonts->getGoogleFonts(), 'label');
        $result = '';

        foreach ($fonts as $font) {
            $result .= $font . '|';
        }

        return substr($result, 0, -1);
    }
}
