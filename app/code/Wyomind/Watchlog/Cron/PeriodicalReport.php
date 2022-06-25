<?php

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\Watchlog\Cron;

/**
 * Class PeriodicalReport
 * @package Wyomind\Watchlog\Cron
 */
class PeriodicalReport
{
    /**
     * @var \Wyomind\Watchlog\Model\ResourceModel\Attempts\CollectionFactory
     */
    protected $_attemptsCollectionFactory;
    public function __construct(\Wyomind\Watchlog\Helper\Delegate $wyomind, \Wyomind\Watchlog\Model\ResourceModel\Attempts\CollectionFactory $attemptsCollectionFactory)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $this->_attemptsCollectionFactory = $attemptsCollectionFactory;
    }
    /**
     * @return \Wyomind\Watchlog\Model\ResourceModel\Attempts\CollectionFactory
     */
    public function getAttemptsCollectionFactory()
    {
        return $this->_attemptsCollectionFactory;
    }
    /**
     * @param \Magento\Cron\Model\Schedule|null $schedule
     * @throws \Exception
     */
    public function sendPeriodicalReport(\Magento\Cron\Model\Schedule $schedule = null)
    {
        try {
            $emails = explode(',', $this->_watchlogHelper->getDefaultConfig('watchlog/periodical_report/emails'));
            if ($this->_watchlogHelper->getDefaultConfig('watchlog/periodical_report/enable_reporting') && count($emails) > 0) {
                $log = [];
                $update = $this->_watchlogHelper->getDefaultConfig('watchlog/periodical_report/last_report');
                if ($update === null) {
                    $update = time() - 16 * 60;
                }
                $cronExpr = json_decode($this->_watchlogHelper->getDefaultConfig('watchlog/periodical_report/cron'));
                $cron['current']['localDate'] = $this->_coreDate->date('l Y-m-d H:i:s');
                $cron['current']['gmtDate'] = $this->_coreDate->gmtDate('l Y-m-d H:i:s');
                $cron['current']['localTime'] = $this->_coreDate->timestamp();
                $cron['current']['gmtTime'] = $this->_coreDate->gmtTimestamp();
                $cron['file']['localDate'] = $this->_coreDate->date('l Y-m-d H:i:s', $update);
                $cron['file']['gmtDate'] = $update;
                $cron['file']['localTime'] = $this->_coreDate->timestamp($update);
                $cron['file']['gmtTime'] = strtotime($update);
                $cron['offset'] = $this->_coreDate->getGmtOffset('hours');
                $this->_logger->notice('-------------------- REPORT PROCESS --------------------');
                $this->_logger->notice('   * Last update : ' . $cron['file']['gmtDate'] . " GMT / " . $cron['file']['localDate'] . ' GMT+' . $cron['offset']);
                $this->_logger->notice('   * Current date : ' . $cron['current']['gmtDate'] . " GMT / " . $cron['current']['localDate'] . ' GMT+' . $cron['offset']);
                $i = 0;
                if (isset($cronExpr) && $cronExpr != null) {
                    foreach ($cronExpr->days as $d) {
                        foreach ($cronExpr->hours as $h) {
                            $time = explode(':', $h);
                            if (date('l', $cron['current']['gmtTime']) == $d) {
                                $cron['tasks'][$i]['localTime'] = strtotime($this->_coreDate->date('Y-m-d')) + $time[0] * 60 * 60 + $time[1] * 60;
                                $cron['tasks'][$i]['localDate'] = date('l Y-m-d H:i:s', $cron['tasks'][$i]['localTime']);
                            } else {
                                $cron['tasks'][$i]['localTime'] = strtotime("last " . $d, $cron['current']['localTime']) + $time[0] * 60 * 60 + $time[1] * 60;
                                $cron['tasks'][$i]['localDate'] = date('l Y-m-d H:i:s', $cron['tasks'][$i]['localTime']);
                            }
                            if ($cron['tasks'][$i]['localTime'] >= $cron['file']['localTime'] && $cron['tasks'][$i]['localTime'] <= $cron['current']['localTime']) {
                                $this->_logger->notice('   * Scheduled : ' . ($cron['tasks'][$i]['localDate'] . " GMT" . $cron['offset']));
                                $this->sendReport($emails);
                                $this->_logger->notice("Report sent to " . implode(", ", $emails));
                                $this->_watchlogHelper->setDefaultConfig('watchlog/periodical_report/last_report', $this->_coreDate->gmtTimestamp());
                                break 2;
                            }
                            $i++;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            if ($schedule != null) {
                $schedule->setStatus('failed');
                $schedule->setMessage($e->getMessage());
                $schedule->save();
            }
            $this->_logger->notice('Process failed');
            $this->_logger->notice($e->getMessage());
        }
    }
    /**
     * @param $date
     * @return array
     */
    public function getHistory($date)
    {
        $history = $this->_attemptsCollectionFactory->create()->getHistory($date);
        $data = [];
        foreach ($history as $line) {
            $data[] = ['ip' => $line->getIp(), 'attempts' => $line->getAttempts(), 'date' => $line->getDate(), 'failed' => $line->getFailed(), 'succeeded' => $line->getSucceeded()];
        }
        return $data;
    }
    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'wyomind_watchlog_periodical_report';
    }
    /**
     * @param $emails
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendReport($emails)
    {
        $template = $this->getTemplate();
        $period = $this->_watchlogHelper->getDefaultConfig('watchlog/periodical_report/report_period');
        if ($period == 0 || $period == '') {
            $date = null;
        } else {
            $date = $this->_coreDate->gmtDate("Y-m-d H:i:s", $this->_coreDate->gmtTimestamp() - $this->_watchlogHelper->getDefaultConfig('watchlog/periodical_report/report_period') * 86400);
        }
        $history = $this->getHistory($date);
        $emailTemplateVariables['log'] = $history;
        $emailTemplateVariables['days'] = $this->_watchlogHelper->getDefaultConfig('watchlog/periodical_report/report_period');
        $emailTemplateVariables['subject'] = $this->_watchlogHelper->getDefaultConfig('watchlog/periodical_report/report_title');
        $transport = $this->_transportBuilder->setTemplateIdentifier($template)->setTemplateOptions(['area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])->setTemplateVars($emailTemplateVariables)->setFrom(['email' => $this->_watchlogHelper->getDefaultConfig('watchlog/periodical_report/sender_email'), 'name' => $this->_watchlogHelper->getDefaultConfig('watchlog/periodical_report/sender_name')])->addTo($emails[0]);
        $count = count($emails);
        for ($i = 1; $i < $count; $i++) {
            $transport->addCc($emails[$i]);
        }
        $transport->getTransport()->sendMessage();
    }
}