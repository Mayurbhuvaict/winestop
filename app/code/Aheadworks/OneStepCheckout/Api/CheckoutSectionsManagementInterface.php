<?php
namespace Aheadworks\OneStepCheckout\Api;

/**
 * Interface CheckoutSectionsManagementInterface
 * @package Aheadworks\OneStepCheckout\Api
 * @api
 */
interface CheckoutSectionsManagementInterface
{
    /**
     * Get sections details
     *
     * @param int $cartId
     * @param \Aheadworks\OneStepCheckout\Api\Data\CheckoutSectionInformationInterface[] $sections
     * @param \Magento\Quote\Api\Data\AddressInterface|null $shippingAddress
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @param int|null $negotiableQuoteId
     * @return \Aheadworks\OneStepCheckout\Api\Data\CheckoutSectionsDetailsInterface
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getSectionsDetails(
        $cartId,
        $sections,
        \Magento\Quote\Api\Data\AddressInterface $shippingAddress = null,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null,
        $negotiableQuoteId = null
    );
}
