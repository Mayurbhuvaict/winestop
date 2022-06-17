<?php
namespace Aheadworks\OneStepCheckout\Model;

use Aheadworks\OneStepCheckout\Api\NewsletterSubscriberManagementInterface;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;

/**
 * Class NewsletterSubscriberManagement
 * @package Aheadworks\OneStepCheckout\Model
 */
class NewsletterSubscriberManagement implements NewsletterSubscriberManagementInterface
{
    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(SubscriberFactory $subscriberFactory)
    {
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function isSubscribedByEmail($email)
    {
        /** @var Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        return $subscriber
            ->loadByEmail($email)
            ->isSubscribed();
    }
}
