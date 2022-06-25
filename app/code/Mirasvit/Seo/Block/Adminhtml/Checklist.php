<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Mirasvit\Seo\Service\ChecklistService;

class Checklist extends Template
{
    protected $_controller;

    protected $_blockGroup;

    protected $_headerText;

    /**
     * @var string
     */
    protected $_template = 'Mirasvit_Seo::checklist.phtml';

    private   $checklistService;

    public function __construct(
        ChecklistService $checklistService,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->checklistService = $checklistService;
    }

    /**
     * @return array
     */
    public function runTests()
    {
        return $this->checklistService->runTests();
    }

    /**
     * @return int
     */
    public function getPassedQTY()
    {
        return $this->checklistService->getPassedQty();
    }

    /**
     * @return int
     */
    public function getTotalQty()
    {
        return $this->checklistService->getTotalQty();
    }

    /**
     * @return int
     */
    public function getFailedQty()
    {
        return $this->checklistService->getFailedQty();
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_checklist';
        $this->_blockGroup = 'Mirasvit_Seo';
        $this->_headerText = __('Checklist');

        parent::_construct();
    }
}
