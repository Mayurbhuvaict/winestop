<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    AdvancedReports
 * @version    2.8.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */



























namespace Aheadworks\AdvancedReports\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping\Factory as DatesGroupingFactory;
use Magento\Framework\App\CacheInterface;
use Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * @package Aheadworks\AdvancedReports\Model
 */
class Config
{
    /**
     * @var string
     */
    const MIN_DATE_CACHE_KEY = 'aw_arep_period_firstdate';

    /**#@+
     * Constants for config path
     */
    const XML_PATH_GENERAL_STORE_INFORMATION_NAME = 'general/store_information/name';
    const XML_PATH_GENERAL_ORDER_STATUS = 'aw_advancedreports/general/order_status';
    const XML_PATH_GENERAL_MANUFACTURER_ATTRIBUTE = 'aw_advancedreports/general/manufacturer_attribute';
    const XML_PATH_GENERAL_LOCALE_FIRSTDAY = 'general/locale/firstday';
    const XML_PATH_GENERAL_REGION_STATE_REQUIRED = 'general/region/state_required';
    const XML_PATH_EMAIL_REPORT_TYPES_TO_EXPORT = 'aw_advancedreports/email/report_types_to_export';
    const XML_PATH_EMAIL_RECIPIENTS = 'aw_advancedreports/email/recipients';
    const XML_PATH_EMAIL_WHEN_TO_SEND_FREQUENCY = 'aw_advancedreports/email/when_to_send_frequency';
    const XML_PATH_EMAIL_REPORT_GROUP_BY = 'aw_advancedreports/email/report_group_by';
    const XML_PATH_EMAIL_REPORT_FORMAT = 'aw_advancedreports/email/report_format';
    const XML_PATH_EMAIL_SENDER = 'aw_advancedreports/email/sender';
    const XML_PATH_EMAIL_SCHEDULED_EMAIL_TEMPLATE = 'aw_advancedreports/email/scheduled_email_template';
    /**#@-*/

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var DatesGroupingFactory
     */
    private $datesGroupingFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param CacheInterface $cache
     * @param DatesGroupingFactory $datesGroupingFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CacheInterface $cache,
        DatesGroupingFactory $datesGroupingFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->cache = $cache;
        $this->datesGroupingFactory = $datesGroupingFactory;
    }

    /**
     * Get order status
     *
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_ORDER_STATUS);
    }

    /**
     * Get manufacturer attribute
     *
     * @return string
     */
    public function getManufacturerAttribute()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_MANUFACTURER_ATTRIBUTE);
    }

    /**
     * Get locale first day of week
     *
     * @return string
     */
    public function getFirstDayOfWeek()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_LOCALE_FIRSTDAY);
    }

    /**
     * Get countries with state required
     *
     * @return array
     */
    public function getCountriesWithStateRequired()
    {
        $value =  $this->scopeConfig->getValue(self::XML_PATH_GENERAL_REGION_STATE_REQUIRED);
        $countries = preg_split('/\,/', $value, 0, PREG_SPLIT_NO_EMPTY);
        return $countries;
    }

    /**
     * Retrieve first available date as string
     *
     * @return string
     */
    public function getFirstAvailableDate()
    {
        if (!$minDate = $this->cache->load(self::MIN_DATE_CACHE_KEY)) {
            try {
                $minDate = $this->datesGroupingFactory->create(DatesGrouping\Day::KEY)->getMinDate();
            } catch (LocalizedException $e) {
                return '';
            }
            $this->cache->save($minDate, self::MIN_DATE_CACHE_KEY, [], null);
        }
        return $minDate;
    }

    /**
     * Get report types to export
     *
     * @param int|null $websiteId
     * @return array
     */
    public function getReportTypesToExport($websiteId = null)
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_REPORT_TYPES_TO_EXPORT,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return $value ? explode(',', $value) : [];
    }

    /**
     * Get email recipients
     *
     * @param int|null $websiteId
     * @return array
     */
    public function getEmailRecipients($websiteId = null)
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_RECIPIENTS,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return $value ? explode(',', $value) : [];
    }

    /**
     * Get when to send frequency
     *
     * @param int|null $websiteId
     * @return string|null
     */
    public function getWhenToSendFrequency($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_WHEN_TO_SEND_FREQUENCY,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Get report group by
     *
     * @param int|null $websiteId
     * @return string|null
     */
    public function getReportGroupBy($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_REPORT_GROUP_BY,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Get report format
     *
     * @param int|null $websiteId
     * @return string|null
     */
    public function getReportFormat($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_REPORT_FORMAT,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve email sender
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getEmailSender($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SENDER,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve sender name
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getSenderName($websiteId = null)
    {
        $sender = $this->getEmailSender($websiteId);
        return $this->scopeConfig->getValue(
            'trans_email/ident_' . $sender . '/name',
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Retrieve sender email
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getSenderEmail($websiteId = null)
    {
        $sender = $this->getEmailSender($websiteId);
        return $this->scopeConfig->getValue(
            'trans_email/ident_' . $sender . '/email',
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Get store name
     *
     * @return string|null
     */
    public function getStoreName()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_STORE_INFORMATION_NAME);
    }

    /**
     * Get scheduled email template
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getScheduledEmailTemplate($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SCHEDULED_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }
}
