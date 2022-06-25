<?php
namespace Aheadworks\OneStepCheckout\Api;

/**
 * Interface CartItemOptionsManagementInterface
 * @package Aheadworks\OneStepCheckout\Api
 */
interface CartItemOptionsManagementInterface
{
    /**
     * Update cart item options
     *
     * @param int $itemId
     * @param string $cartId
     * @param string $options
     * @return \Aheadworks\OneStepCheckout\Api\Data\CartItemOptionsDetailsInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function update($itemId, $cartId, $options);
}
