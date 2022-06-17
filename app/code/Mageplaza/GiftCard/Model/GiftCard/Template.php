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

namespace Mageplaza\GiftCard\Model\GiftCard;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Mageplaza\GiftCard\Model\TemplateFactory;

/**
 * Class Template
 * @package Mageplaza\GiftCard\Model\GiftCard
 */
class Template extends AbstractSource
{
    /**
     * @var TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var array
     */
    protected $_templates;

    /**
     * Constructor
     *
     * @param TemplateFactory $templateFactory
     */
    public function __construct(TemplateFactory $templateFactory)
    {
        $this->_templateFactory = $templateFactory;
    }

    /**
     * Get all option
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_templates === null) {
            $this->_templates = [['value' => 'no_template', 'label' => __('-- No Template --')]];

            $collection = $this->_templateFactory->create()->getCollection();
            foreach ($collection as $template) {
                $this->_templates[] = ['value' => $template->getId(), 'label' => $template->getName()];
            }
        }

        return $this->_templates;
    }
}
