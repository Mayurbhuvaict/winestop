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






















































































































































































































































































namespace Aheadworks\AdvancedReports\Ui\Component\Listing;

use Aheadworks\AdvancedReports\Model\Url\Base64Coder;
use Magento\Ui\Component\Container;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class Breadcrumbs
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing
 */
class Breadcrumbs extends Container
{
    /**
     * @var string
     */
    const SESSION_KEY = 'aw_arep_breadcrumbs';

    /**
     * @var string
     */
    const BREADCRUMBS_CONTROLLER_TITLE = 'aw_arep_controller_title';

    /**
     * @var bool
     */
    private $isValidCrumbs = true;

    /**
     * @var array
     */
    private $crumbsFromUrl = [];

    /**
     * @var string
     */
    private $brcParam;

    /**
     * @var array
     */
    private $mapCrumbs = [
        'salesoverview-productperformance',
        'salesoverview-productperformance-productperformance_variantperformance',
        'productperformance-productperformance_variantperformance',
        'conversion-productconversion',
        'conversion-productconversion-productconversion_variant',
        'category-productperformance',
        'category-productperformance-productperformance_variantperformance',
        'couponcode-salesoverview',
        'couponcode-salesoverview-productperformance',
        'couponcode-salesoverview-productperformance-productperformance_variantperformance',
        'manufacturer-productperformance',
        'manufacturer-productperformance-productperformance_variantperformance',
        'paymenttype-salesoverview',
        'paymenttype-salesoverview-productperformance',
        'paymenttype-salesoverview-productperformance-productperformance_variantperformance',
        'location-location_region',
        'location-location_region-location_city',
        'customersales-customersales_customers'
    ];

    /**
     * @var array
     */
    private $defaultCrumbs = [
        'salesoverview' => [
            'label' => 'Sales Overview',
            'url' => '*/salesoverview/index',
            'last' => false,
            'allowed_url_param' => [
                'coupon_code' => 'label',
                'payment_name' => 'label',
                'payment_type' => 'param'
            ]
        ],
        'productperformance' => [
            'label' => 'Product Performance',
            'url' => '*/productperformance/index',
            'last' => false,
            'allowed_url_param' => [
                'coupon_code' => 'label',
                'payment_name' => 'label',
                'payment_type' => 'param',
                'manufacturer' => 'label',
                'category_name' => 'label',
                'category_id' => 'param'
            ]
        ],
        'productperformance_variantperformance' => [
            'label' => 'Product Variation Performance',
            'url' => '*/productperformance_variantperformance/index',
            'last' => false,
            'allowed_url_param' => [
                'product_name' => 'label',
                'product_id' => 'param'
            ]
        ],
        'category' => [
            'label' => 'Sales by Category',
            'url' => '*/category/index',
            'last' => false
        ],
        'couponcode' => [
            'label' => 'Sales by Coupon Code',
            'url' => '*/couponcode/index',
            'last' => false
        ],
        'paymenttype' => [
            'label' => 'Sales by Payment Type',
            'url' => '*/paymenttype/index',
            'last' => false
        ],
        'manufacturer' => [
            'label' => 'Sales by Manufacturer',
            'url' => '*/manufacturer/index',
            'last' => false
        ],
        'conversion' => [
            'label' => 'Traffic and Conversions',
            'url' => '*/conversion/index',
            'last' => false
        ],
        'productconversion' => [
            'label' => 'Product Conversion',
            'url' => '*/productconversion/index',
            'last' => false,
            'allowed_url_param' => [
                'product_name' => 'label',
                'product_id' => 'param'
            ]
        ],
        'location' => [
            'label' => 'Sales by Location',
            'url' => '*/location/index',
            'last' => false,
        ],
        'location_region' => [
            'label' => 'Sales by State/Region',
            'url' => '*/location_region/index',
            'last' => false,
            'allowed_url_param' => [
                'country_name' => 'label',
                'country_id' => 'param'
            ]
        ],
        'location_city' => [
            'label' => 'Sales by City/Place',
            'url' => '*/location_city/index',
            'last' => false,
            'allowed_url_param' => [
                'country_id' => 'param',
                'country_name' => 'label',
                'region' => 'label',
            ]
        ],
        'customersales' => [
            'label' => 'Customer Sales',
            'url' => '*/customersales/index',
            'last' => false,
        ],
        'customersales_customers' => [
            'label' => 'Customers',
            'url' => '*/customersales_customers/index',
            'last' => false,
            'allowed_url_param' => [
                'range_from' => 'param',
                'range_to' => 'param',
                'range_title' => 'param'
            ]
        ],
    ];

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param ContextInterface $context
     * @param SessionManagerInterface $session
     * @param RequestInterface $request
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        SessionManagerInterface $session,
        RequestInterface $request,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->session = $session;
        $this->request = $request;
        $this->validate();
    }

    /**
     * {@inheritdoc}
     */
    protected function validate()
    {
        $crumbsFromUrl = $this->getContext()->getRequestParam('brc');
        if ($crumbsFromUrl && !$this->request->isAjax()) {
            $this->crumbsFromUrl = explode('-', $crumbsFromUrl);
            if (!in_array($crumbsFromUrl, $this->mapCrumbs)) {
                $this->isValidCrumbs = false;
            }
        } else {
            $this->isValidCrumbs = false;
        }
    }

    /**
     * Add crumb
     *
     * @param string $key
     * @param string $alias
     * @param string $label
     * @param string $url
     * @return $this
     */
    public function addCrumb($key, $alias, $label, $url)
    {
        $sessionCrumbs = $this->session->getData(self::SESSION_KEY) ?: [];
        $sessionCrumbs[$key][$alias] = [
            'label' => $label,
            'url' => $url,
            'last' => false
        ];
        $this->session->setData(self::SESSION_KEY, $sessionCrumbs);
        return $this;
    }

    /**
     * Retrieve crumbs
     *
     * @return array
     */
    public function getCrumbs()
    {
        $crumbs = [];
        if (!$this->isValidCrumbs) {
            return $crumbs;
        }

        if ($sessionCrumbs = $this->session->getData(self::SESSION_KEY)) {
            $lastCrumb = $this->getFirstLastCrumb(false);
            foreach ($sessionCrumbs[$this->getFirstLastCrumb()] as $key => $value) {
                $crumb = $value;
                if ($lastCrumb == $key) {
                    $crumb['last'] = true;
                    $crumbs[] = $crumb;
                    break;
                }
                $crumbs[] = $crumb;
            }
        }
        return count($crumbs) > 1 ? $crumbs : [];
    }

    /**
     * Retrieve the first|last key crumb
     *
     * @param bool $first
     * @return string
     */
    public function getFirstLastCrumb($first = true)
    {
        $key = $this->request->getControllerName();
        if ($crumbs = $this->crumbsFromUrl) {
            $key = $first ? reset($crumbs) : end($crumbs);
        }
        return $key;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        if (!$this->isValidCrumbs) {
            return;
        }
        $this->prepareCrumbs();

        $config = $this->getData('config');
        $config['crumbs'] = $this->getCrumbs();
        $this->setData('config', $config);
    }

    /**
     * Prepare crumbs
     *
     * @return void
     */
    private function prepareCrumbs()
    {
        $addCrumb = false;
        $firstCrumb = $this->getFirstLastCrumb();
        $sessionCrumbs = $this->session->getData(self::SESSION_KEY);
        if ($sessionCrumbs && array_key_exists($firstCrumb, $sessionCrumbs)) {
            $addCrumb = true;
        } else {
            if ($this->crumbsFromUrl) {
                $this->brcParam = '';
                foreach ($this->crumbsFromUrl as $crumb) {
                    list($url, $label) = $this->getUrlLabelByDefaultCrumb($crumb, $firstCrumb);
                    $this->addCrumb($firstCrumb, $crumb, $label, $url);
                }
            } else {
                $addCrumb = true;
            }
        }

        if ($addCrumb) {
            $filterPool = $this->context->getDataProvider()->getDefaultFilterPool();
            $periodFilter = $filterPool->getFilter('period');
            $groupByFilter = $filterPool->getFilter('group_by');

            $query = [
                'period_type' => $periodFilter->getPeriodType(),
                'group_by' => $groupByFilter->getValue(),
                'period_from' => $periodFilter->getPeriodFrom()->format('Y-m-d'),
                'period_to' => $periodFilter->getPeriodTo()->format('Y-m-d')
            ];
            $currentQuery = $this->request->getQueryValue();
            $query = array_merge($query, $currentQuery);

            $this->addCrumb(
                $this->getFirstLastCrumb(),
                $this->request->getControllerName(),
                $this->session->getData(self::BREADCRUMBS_CONTROLLER_TITLE),
                $this->getContext()->getUrl('*/*/*', ['_current' => true, '_query' => $query])
            );
        }
    }

    /**
     * Retrieve url and label by default crumb
     *
     * @param string $crumb
     * @param string $firstCrumb
     * @return array
     */
    private function getUrlLabelByDefaultCrumb($crumb, $firstCrumb)
    {
        if ($firstCrumb == $crumb) {
            $url = $this->getContext()->getUrl($this->defaultCrumbs[$crumb]['url']);
            $label = __($this->defaultCrumbs[$crumb]['label']);
            $this->brcParam .= $crumb;
        } else {
            $this->brcParam .= '-' . $crumb;
            $query = ['brc' => $this->brcParam];
            $label = __($this->defaultCrumbs[$crumb]['label']);
            foreach ($this->request->getQueryValue() as $key => $value) {
                if (array_key_exists($key, $this->defaultCrumbs[$crumb]['allowed_url_param'])) {
                    $query[$key] = $value;
                    if ($this->defaultCrumbs[$crumb]['allowed_url_param'][$key] == 'label') {
                        $label = $this->getLabelByQueryParam($key, $value, $this->defaultCrumbs[$crumb]['label']);
                    }
                }
            }
            $url = $this->getContext()->getUrl($this->defaultCrumbs[$crumb]['url'], ['_query' => $query]);
        }
        return [$url, $label];
    }

    /**
     * Retrieve label by query param
     *
     * @param string $key
     * @param mixed $value
     * @param string $label
     * @return \Magento\Framework\Phrase
     */
    private function getLabelByQueryParam($key, $value, $label)
    {
        switch ($key) {
            case 'payment_name':
            case 'product_name':
            case 'category_name':
            case 'country_name':
                $value = $this->request->getParam($key);
                break;
        }
        if ($value) {
            $label = __($label . ' (%1)', Base64Coder::decode($value));
        } else {
            $label = __($label);
        }
        return $label;
    }
}
