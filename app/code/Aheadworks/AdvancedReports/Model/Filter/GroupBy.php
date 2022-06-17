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

































































































































































































namespace Aheadworks\AdvancedReports\Model\Filter;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Aheadworks\AdvancedReports\Model\Source\Groupby as GroupbySource;

/**
 * Class GroupBy
 *
 * @package Aheadworks\AdvancedReports\Model\Filter
 */
class GroupBy implements FilterInterface
{
    /**
     * @var string
     */
    const SESSION_KEY = 'aw_arep_groupby';

    /**
     * @var string
     */
    private $groupBy;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var bool
     */
    private $isCacheUsed;

    /**
     * @param RequestInterface $request
     * @param SessionManagerInterface $session
     * @param bool $isCacheUsed
     */
    public function __construct(
        RequestInterface $request,
        SessionManagerInterface $session,
        $isCacheUsed = false
    ) {
        $this->request = $request;
        $this->session = $session;
        $this->isCacheUsed = $isCacheUsed;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        if (!$this->isCacheUsed) {
            $this->groupBy = null;
        }
        if (!$this->groupBy) {
            $this->groupBy = $this->request->getParam('group_by');
            if (!$this->groupBy) {
                $this->groupBy = $this->session->getData(self::SESSION_KEY);
            }
            if (!$this->groupBy) {
                $this->groupBy = $this->getDefaultValue();
            }
            $this->session->setData(self::SESSION_KEY, $this->groupBy);
        }
        return $this->groupBy;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue()
    {
        return GroupbySource::TYPE_MONTH;
    }
}
