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

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Container;
use Aheadworks\AdvancedReports\Model\Source\ProductAttributes\Attributes;

/**
 * Class Conditions
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing
 */
class Conditions extends Container
{
    /**
     * @var Attributes
     */
    private $attributes;

    /**
     * @param ContextInterface $context
     * @param Attributes $attributes
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Attributes $attributes,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->attributes = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $config = $this->getData('config');
        $config['attributesCount'] = $this->attributes->getOptionCount();
        $this->setData('config', $config);
        parent::prepare();
    }
}
