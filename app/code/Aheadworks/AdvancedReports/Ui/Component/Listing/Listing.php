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

use Magento\Ui\Component\Listing as ListingComponent;
use Aheadworks\AdvancedReports\Ui\DataProvider\Listing\DataModifierComposite;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Listing
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing
 */
class Listing extends ListingComponent
{
    /**
     * @var DataModifierComposite
     */
    protected $dataModifier;

    /**
     * @param ContextInterface $context
     * @param DataModifierComposite $dataModifier
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        DataModifierComposite $dataModifier,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->dataModifier = $dataModifier;
    }

    /**
     * @inheritdoc
     *
     * @throws /Exception
     */
    public function getDataSourceData()
    {
        $data = $this->getContext()->getDataProvider()->getData();
        $data = $this->dataModifier->prepareSourceData($data, $this);
        return ['data' => $data];
    }

    /**
     * @inheritdoc
     *
     * @throws /Exception
     */
    public function prepare()
    {
        parent::prepare();
        $this->dataModifier->prepareComponentData($this);
    }
}
