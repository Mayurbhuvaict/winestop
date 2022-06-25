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































































































































































namespace Aheadworks\AdvancedReports\Model\Source;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CustomerGroup
 *
 * @package Aheadworks\AdvancedReports\Model\Source
 */
class CustomerGroup implements OptionSourceInterface
{
    /**
     * @var GroupManagementInterface
     */
    private $groupManagement;

    /**
     * @var array
     */
    private $options;

    /**
     * @param GroupManagementInterface $groupManagement
     */
    public function __construct(
        GroupManagementInterface $groupManagement
    ) {
        $this->groupManagement = $groupManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            /** @var GroupInterface $allGroup */
            $allGroup = $this->groupManagement->getAllCustomersGroup();
            $this->options[] = ['value' => $allGroup->getId(), 'label' => __('All Groups')];

            /** @var GroupInterface $notLoggedInGroup */
            $notLoggedInGroup = $this->groupManagement->getNotLoggedInGroup();
            $this->options[] = ['value' => $notLoggedInGroup->getId(), 'label' => $notLoggedInGroup->getCode()];

            /** @var GroupInterface $group */
            foreach ($this->groupManagement->getLoggedInGroups() as $group) {
                $this->options[] = ['value' => $group->getId(), 'label' => $group->getCode()];
            }
        }
        return $this->options;
    }
}
