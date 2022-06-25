<?php
namespace Aheadworks\OneStepCheckout\Api;

/**
 * Interface NewsletterSubscriberManagementInterface
 * @package Aheadworks\OneStepCheckout\Api
 * @api
 */
interface NewsletterSubscriberManagementInterface
{
    /**
     * Check if there is a newsletter subscription by given email
     *
     * @param string $email
     * @return bool
     */
    public function isSubscribedByEmail($email);
}
