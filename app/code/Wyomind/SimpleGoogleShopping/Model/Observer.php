<?php

/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\SimpleGoogleShopping\Model;

/**
 * Simple Google Shopping observer
 */
class Observer
{
    /**
     * @var ResourceModel\Feeds\CollectionFactory
     */
    protected $_collectionFactory = null;
    /**
     * @var \Wyomind\SimpleGoogleShopping\Logger\LoggerCron
     */
    protected $_logger = null;
    public function __construct(\Wyomind\SimpleGoogleShopping\Helper\Delegate $wyomind, \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\CollectionFactory $collectionFactory)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $this->_collectionFactory = $collectionFactory;
        $this->_logger = $this->objectManager->create('Wyomind\\SimpleGoogleShopping\\Logger\\LoggerCron');
    }
    /**
     * Check if google shopping data feeds need to be generated
     * @param \Magento\Cron\Model\Schedule $schedule
     */
    public function checkToGenerate(\Magento\Cron\Model\Schedule $schedule)
    {
        try {
            $log = [];
            $this->_logger->notice("-------------------- CRON PROCESS --------------------");
            $log[] = "-------------------- CRON PROCESS --------------------";
            $coll = $this->_collectionFactory->create();
            $cnt = 0;
            $first = true;
            foreach ($coll as $feed) {
                $done = false;
                try {
                    $feed->isCron = true;
                    if ($first) {
                        $feed->loadCustomFunctions();
                        $first = false;
                    }
                    $this->_logger->notice("--> Running profile : " . $feed->getSimplegoogleshoppingFilename() . " [#" . $feed->getSimplegoogleshoppingId() . "] <--");
                    $log[] = "--> Running profile : " . $feed->getSimplegoogleshoppingFilename() . " [#" . $feed->getSimplegoogleshoppingId() . "] <--";
                    $cron = [];
                    $offset = $this->_coreDate->getGmtOffset();
                    $cron['current']['localeDate'] = $this->_coreDate->date('l Y-m-d H:i:s', time() + $offset);
                    $cron['current']['gmtDate'] = $this->_coreDate->gmtDate('l Y-m-d H:i:s');
                    $cron['current']['localeTime'] = $this->_coreDate->timestamp(time() + $offset);
                    $cron['current']['gmtTime'] = $this->_coreDate->gmtTimestamp();
                    $cron['file']['localeDate'] = $this->_coreDate->date('l Y-m-d H:i:s', strtotime($feed->getSimplegoogleshoppingTime()) + $offset);
                    $cron['file']['gmtDate'] = $this->_coreDate->gmtdate('l Y-m-d H:i:s', $feed->getSimplegoogleshoppingTime());
                    $cron['file']['localeTime'] = $this->_coreDate->timestamp(strtotime($feed->getSimplegoogleshoppingTime()) + $offset);
                    $cron['file']['gmtTime'] = strtotime($feed->getSimplegoogleshoppingTime());
                    $cron['offset'] = $this->_coreDate->getGmtOffset("hours");
                    $log[] = '   * Last update : ' . $cron['file']['gmtDate'] . " GMT / " . $cron['file']['localeDate'] . ' GMT ' . $cron['offset'] . "";
                    $log[] = '   * Current date : ' . $cron['current']['gmtDate'] . " GMT / " . $cron['current']['localeDate'] . ' GMT ' . $cron['offset'] . "";
                    $this->_logger->notice('   * Last update : ' . $cron['file']['gmtDate'] . " GMT / " . $cron['file']['localeDate'] . ' GMT ' . $cron['offset']);
                    $this->_logger->notice('   * Current date : ' . $cron['current']['gmtDate'] . " GMT / " . $cron['current']['localeDate'] . ' GMT ' . $cron['offset']);
                    $cronExpr = json_decode($feed->getCronExpr());
                    $i = 0;
                    if ($cronExpr != null && isset($cronExpr->days)) {
                        foreach ($cronExpr->days as $d) {
                            foreach ($cronExpr->hours as $h) {
                                $time = explode(':', $h);
                                if (date('l', $cron['current']['gmtTime']) == $d) {
                                    $cron['tasks'][$i]['localeTime'] = strtotime($this->_coreDate->date('Y-m-d', time() + $offset)) + $time[0] * 60 * 60 + $time[1] * 60;
                                    $cron['tasks'][$i]['localeDate'] = date('l Y-m-d H:i:s', $cron['tasks'][$i]['localeTime']);
                                } else {
                                    $cron['tasks'][$i]['localeTime'] = strtotime("last " . $d, $cron['current']['localeTime']) + $time[0] * 60 * 60 + $time[1] * 60;
                                    $cron['tasks'][$i]['localeDate'] = date('l Y-m-d H:i:s', $cron['tasks'][$i]['localeTime']);
                                }
                                if ($cron['tasks'][$i]['localeTime'] >= $cron['file']['localeTime'] && $cron['tasks'][$i]['localeTime'] <= $cron['current']['localeTime'] && $done != true) {
                                    $this->_logger->notice('   * Scheduled : ' . ($cron['tasks'][$i]['localeDate'] . " GMT" . $cron['offset']));
                                    $log[] = '   * Scheduled : ' . ($cron['tasks'][$i]['localeDate'] . " GMT" . $cron['offset']) . "";
                                    $this->_logger->notice("   * Starting generation");
                                    $result = $feed->generateXml();
                                    if ($result === $feed) {
                                        $done = true;
                                        $this->_logger->notice("   * EXECUTED!");
                                        $log[] = "   * EXECUTED!";
                                    } else {
                                        $this->_logger->notice("   * ERROR! " . $result);
                                        $log[] = "   * ERROR! " . $result . "";
                                    }
                                    $cnt++;
                                    break 2;
                                }
                                $i++;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    $cnt++;
                    $this->_logger->notice("   * ERROR! " . $e->getMessage());
                    $log[] = "   * ERROR! " . $e->getMessage() . "";
                }
                if (!$done) {
                    $this->_logger->notice("   * SKIPPED!");
                    $log[] = "   * SKIPPED!";
                }
            }
            if ($this->licenseHelper->getStoreConfig("simplegoogleshopping/settings/enable_reporting")) {
                $emails = explode(',', $this->licenseHelper->getStoreConfig("simplegoogleshopping/settings/emails"));
                if (count($emails) > 0) {
                    try {
                        if ($cnt) {
                            $template = "wyomind_simplegoogleshopping_cron_report";
                            $transport = $this->_transportBuilder->setTemplateIdentifier($template)->setTemplateOptions(['area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])->setTemplateVars(['report' => implode("<br/>", $log), 'subject' => $this->licenseHelper->getStoreConfig('simplegoogleshopping/settings/report_title')])->setFrom(['email' => $this->licenseHelper->getStoreConfig('simplegoogleshopping/settings/sender_email'), 'name' => $this->licenseHelper->getStoreConfig('simplegoogleshopping/settings/sender_name')])->addTo($emails[0]);
                            $count = count($emails);
                            for ($i = 1; $i < $count; $i++) {
                                $transport->addCc($emails[$i]);
                            }
                            $transport->getTransport()->sendMessage();
                        }
                    } catch (\Exception $e) {
                        $this->_logger->notice('   * EMAIL ERROR! ' . $e->getMessage());
                        $log[] = '   * EMAIL ERROR! ' . $e->getMessage();
                    }
                }
            }
        } catch (\Exception $e) {
            $schedule->setStatus('failed');
            $schedule->setMessage($e->getMessage());
            $schedule->save();
            $this->_logger->notice("MASSIVE ERROR ! ");
            $this->_logger->notice($e->getMessage());
        }
    }
}