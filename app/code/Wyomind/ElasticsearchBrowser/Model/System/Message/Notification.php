<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchBrowser\Model\System\Message;

class Notification implements \Magento\Framework\Notification\MessageInterface
{
    /**
     * Message identity
     */
    const MESSAGE_IDENTITY = 'wyomind_elasticsearchbrowser_notification';
    /**
     * @var boolean
     */
    public $_warnings = 0;
    /**
     * @var string
     */
    public $_content = '';
    public function __construct(\Wyomind\ElasticsearchBrowser\Helper\Delegate $wyomind)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
    }
    public function checkNotifications()
    {
        $html = '';
        $notificationEnabled = $this->_configHelper->isServerStatusBackendNotificationEnabled();
        // Server failed notifications
        if ($notificationEnabled) {
            try {
                $this->_client->init();
                $this->_client->info();
            } catch (\Exception $e) {
                $this->_warnings++;
                $url = $this->_urlBuilder->getUrl('admin/system_config/edit/section/catalog');
                $html .= '<div><b>Elasticsearch Server failed</b><br/>' . __('Reason: ') . $e->getMessage() . '<br/>' . __('Please check your Elasticsearch Server configuration from <a href="' . $url . '">Stores > Configuration > Catalog > Catalog > Catalog Search</a>.') . '</div><br/>';
            }
        }
        $this->_content = $html;
    }
    /**
     * Retrieve unique system message identity
     * @return string
     */
    public function getIdentity()
    {
        return self::MESSAGE_IDENTITY;
    }
    /**
     * Check whether the system message should be shown
     * @return bool
     */
    public function isDisplayed()
    {
        $this->checkNotifications();
        return $this->_warnings > 0;
    }
    /**
     * Retrieve system message text
     * @return string
     */
    public function getText()
    {
        return $this->_content;
    }
    /**
     * Retrieve system message severity
     * Possible default system message types:
     *  - MessageInterface::SEVERITY_CRITICAL
     *  - MessageInterface::SEVERITY_MAJOR
     *  - MessageInterface::SEVERITY_MINOR
     *  - MessageInterface::SEVERITY_NOTICE
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_MAJOR;
    }
}