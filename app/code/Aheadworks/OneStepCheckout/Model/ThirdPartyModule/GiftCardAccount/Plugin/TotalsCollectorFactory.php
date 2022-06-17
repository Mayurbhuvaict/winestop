<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\GiftCardAccount\Plugin;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class TotalsCollectorFactory
 *
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\GiftCardAccount\Plugin
 */
class TotalsCollectorFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create totals collector plugin
     *
     * @return mixed
     * @throws \Exception
     */
    public function create()
    {
        return $this->objectManager->create(\Magento\GiftCardAccount\Model\Plugin\TotalsCollector::class);
    }
}
