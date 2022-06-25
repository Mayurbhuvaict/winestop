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

/**
 * Class Range
 *
 * @package Aheadworks\AdvancedReports\Model\Filter
 */
class Range implements FilterInterface
{
    /**
     * @var string
     */
    const SESSION_RANGE_FROM_KEY = 'aw_rep_range_from_key';

    /**
     * @var string
     */
    const SESSION_RANGE_TO_KEY = 'aw_rep_range_to_key';

    /**
     * @var array
     */
    private $rangeCache;

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
            $this->rangeCache = null;
        }
        if (null == $this->rangeCache) {
            $from = $this->request->getParam('range_from');
            $to = $this->request->getParam('range_to');
            if ($from == null && $to == null) {
                $from = $this->session->getData(self::SESSION_RANGE_FROM_KEY);
                $to = $this->session->getData(self::SESSION_RANGE_TO_KEY);
                if ($from == null && $to == null) {
                    $this->rangeCache = null;
                    return $this->rangeCache;
                }
            }
            $this->session->setData(self::SESSION_RANGE_FROM_KEY, $from);
            $this->session->setData(self::SESSION_RANGE_TO_KEY, $to);
            $this->rangeCache = [
                'from' => $from,
                'to'   => $to,
            ];
        }
        return $this->rangeCache;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue()
    {
        return [
            'from' => null,
            'to' => null,
        ];
    }
}
