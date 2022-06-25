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



namespace Mirasvit\SeoContent\Model\Template\Rule\Condition;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\UrlInterface;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

/**
 * @method getAttribute()
 * @method getJsFormObject()
 */
class CategoryCondition extends AbstractCondition
{
    /**
     * @var UrlInterface
     */
    private $urlManager;

    /**
     * @var Context
     */
    private $context;

    /**
     * CategoryCondition constructor.
     * @param UrlInterface $urlManager
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlManager,
        Context $context,
        array $data = []
    ) {
        $this->urlManager = $urlManager;
        $this->context    = $context;

        parent::__construct($context, $data);
    }

    /**
     * @return $this|AbstractCondition
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'category_ids' => (string)__('Category'),
        ];

        asort($attributes);
        $this->setData('attribute_option', $attributes);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueAfterElementHtml()
    {
        $html = '';

        switch ($this->getAttribute()) {
            case 'category_ids':
                $image = $this->context->getAssetRepository()->getUrl('images/rule_chooser_trigger.gif');
                break;
        }

        if (!empty($image)) {
            $html
                = '<a href="javascript:void(0)" class="rule-chooser-trigger">
                    <img src="' . $image . '" alt="" class="v-middle rule-chooser-trigger" title="' . __('Open Chooser') . '" />
                    </a>';
        }

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueElementChooserUrl()
    {
        $url = false;
        switch ($this->getAttribute()) {
            case 'category_ids':
                $url = 'catalog_rule/promo_widget/chooser/attribute/' . $this->getAttribute();
                if ($this->getJsFormObject()) {
                    $url .= '/form/' . $this->getJsFormObject();
                } else {
                    $url .= '/form/rule_conditions_fieldset';
                }
                break;
        }

        return $url !== false ? $this->urlManager->getUrl($url) : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getExplicitApply()
    {
        switch ($this->getAttribute()) {
            case 'category_ids':
                return true;
        }

        return false;
    }

    /**
     * @param AbstractModel $object
     *
     * @return bool
     */
    public function validate(AbstractModel $object)
    {
        if (!($object instanceof \Magento\Catalog\Model\Category)) {
            return true;
        }

        $attrCode = $this->getAttribute();

        switch ($attrCode) {
            case 'category_ids':
                $categoryIds = [$object->getId()];

                return $this->validateAttribute($categoryIds);
        }

        return parent::validate($object);
    }
}
