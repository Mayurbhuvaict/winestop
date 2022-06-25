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

namespace Mageplaza\GiftCard\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Template
 * @package Mageplaza\GiftCard\Model\ResourceModel
 */
class Template extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('mageplaza_giftcard_template', 'template_id');
    }

    /**
     * @param AbstractModel|\Mageplaza\GiftCard\Model\Template $object
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $required = [
            'name'        => $object->getName(),
            'title'       => $object->getTitle(),
            'font_family' => $object->getFontFamily(),
        ];

        foreach ($required as $key => $value) {
            if ($value !== null && $value === '') {
                throw new LocalizedException(__('%1 value must not be empty', $key));
            }
        }

        parent::_beforeSave($object);

        return $this;
    }
}
