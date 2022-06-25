<?php

/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\SimpleGoogleShopping\Block\Adminhtml\Feeds;

/**
 * Preview block
 */
class Preview extends \Magento\Backend\Block\Template
{
    public function __construct(\Wyomind\SimpleGoogleShopping\Helper\Delegate $wyomind, \Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        parent::__construct($context, $data);
    }
    /**
     * Get content of the block
     * @return string
     * @throws \Exception
     */
    public function getContent()
    {
        $request = $this->getRequest();
        $id = $request->getParam('simplegoogleshopping_id');
        if ($id != 0) {
            try {
                $this->sgsModel->load($id);
                $this->sgsModel->limit = $this->licenseHelper->getStoreConfig('simplegoogleshopping/system/preview');
                $this->sgsModel->setDisplay(true);
                return $this->sgsModel->generateXml($request);
            } catch (\Exception $e) {
                return __('Unable to generate the data feed : ' . $e->getMessage());
            }
        }
        return '';
    }
}