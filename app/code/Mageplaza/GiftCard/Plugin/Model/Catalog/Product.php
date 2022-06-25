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

namespace Mageplaza\GiftCard\Plugin\Model\Catalog;

use Closure;
use Exception;
use Magento\Framework\App\Request\Http\Proxy;
use Magento\Framework\App\RequestInterface;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\Product\Type\GiftCard;
use Mageplaza\GiftCard\Ui\DataProvider\Product\Modifier\GiftCard as GiftCardField;

/**
 * Class Product
 * @package Mageplaza\GiftCard\Plugin\Model\Catalog
 */
class Product
{
    /**
     * @var RequestInterface|Proxy
     */
    private $request;

    /**
     * @var Data
     */
    private $helper;

    /**
     * Product constructor.
     *
     * @param RequestInterface $request
     * @param Data $helper
     */
    public function __construct(
        RequestInterface $request,
        Data $helper
    ) {
        $this->request = $request;
        $this->helper  = $helper;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product $subject
     * @param Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return mixed
     * @throws Exception
     */
    public function aroundSave(\Magento\Catalog\Model\ResourceModel\Product $subject, Closure $proceed, $product)
    {
        $data = $this->request->getPostValue();

        if (empty($data['rule']) || $product->getTypeId() !== GiftCard::TYPE_GIFTCARD) {
            return $proceed($product);
        }

        $conditions = $this->helper->convertConditions($data['rule']);

        $product->setData(GiftCardField::FIELD_CONDITIONS, $conditions);

        if ($product->getId()) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product $resource */
            $resource = $product->getResource();

            $resource->saveAttribute($product, GiftCardField::FIELD_CONDITIONS);
        }

        return $proceed($product);
    }
}
