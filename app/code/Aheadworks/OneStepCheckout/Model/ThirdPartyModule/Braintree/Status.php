<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Braintree;

use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class Status
 *
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Braintree
 */
class Status implements StatusInterface
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ProductMetadataInterface $productMetadata
    ) {
        $this->productMetadata = $productMetadata;
    }

    /**
     * @inheritdoc
     */
    public function isPayPalInContextMode()
    {
        $magentoVersion = $this->productMetadata->getVersion();
        return version_compare($magentoVersion, '2.3.3', '>=');
    }
}
