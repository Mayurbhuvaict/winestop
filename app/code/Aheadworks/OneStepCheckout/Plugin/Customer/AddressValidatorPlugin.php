<?php
namespace Aheadworks\OneStepCheckout\Plugin\Customer;

use Magento\Customer\Model\Address\Validator\General;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Aheadworks\OneStepCheckout\Model\Address\Validator as AddressValidator;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AddressValidatorPlugin
 *
 * @package Aheadworks\OneStepCheckout\Plugin\Customer
 */
class AddressValidatorPlugin
{
    /**
     * @var AddressValidator
     */
    private $addressValidator;

    /**
     * @param AddressValidator $addressValidator
     */
    public function __construct(
        AddressValidator $addressValidator
    ) {
        $this->addressValidator = $addressValidator;
    }

    /**
     * Rewrite validator in order to avoid validation of required attributes
     *
     * @param General $subject
     * @param callable $proceed
     * @param AbstractAddress $address
     * @return array
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function aroundValidate(General $subject, callable $proceed, AbstractAddress $address)
    {
        if ($address instanceof QuoteAddress) {
            $errors = $this->addressValidator->validate($address);
        } else {
            $errors = $proceed($address);
        }

        return $errors;
    }
}
