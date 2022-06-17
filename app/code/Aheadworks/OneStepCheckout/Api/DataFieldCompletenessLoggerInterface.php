<?php
namespace Aheadworks\OneStepCheckout\Api;

/**
 * Interface DataFieldCompletenessLoggerInterface
 * @package Aheadworks\OneStepCheckout\Api
 */
interface DataFieldCompletenessLoggerInterface
{
    /**
     * Log field completeness data
     *
     * @param int $cartId
     * @param \Aheadworks\OneStepCheckout\Api\Data\DataFieldCompletenessInterface[] $fieldCompleteness
     * @return void
     */
    public function log($cartId, array $fieldCompleteness);
}
