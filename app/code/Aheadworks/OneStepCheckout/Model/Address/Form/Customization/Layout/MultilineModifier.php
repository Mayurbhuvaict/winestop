<?php
namespace Aheadworks\OneStepCheckout\Model\Address\Form\Customization\Layout;

use Aheadworks\OneStepCheckout\Model\Config;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class MultilineModifier
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\Customization\Layout
 */
class MultilineModifier
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param Config $config
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        Config $config,
        ArrayManager $arrayManager
    ) {
        $this->config = $config;
        $this->arrayManager = $arrayManager;
    }

    /**
     * Modify multiline address field config
     *
     * @param array $rowLayout
     * @param string $addressType
     * @return array
     */
    public function modify($rowLayout, $addressType)
    {
        // todo: revise, should be more abstract
        $formConfig = $this->config->getAddressFormConfig($addressType);
        if (isset($formConfig['attributes']['street'])) {
            $streetAttribute = $formConfig['attributes']['street'];
            foreach ($streetAttribute as $line => $config) {
                $streetElemPath =  $this->arrayManager->findPath('street' . $line, $rowLayout, null);
                $streetElem = $this->arrayManager->get($streetElemPath, $rowLayout);
                $streetLinesLayoutUpdate = [];
                if ($streetElem && isset($formConfig['attributes']['street'][$line])) {
                    $streetConfigUpdate = $formConfig['attributes']['street'][$line];
                    $streetConfigUpdate['label'] = __($streetConfigUpdate['label']);
                    if ((bool)$streetConfigUpdate['visible']) {
                        $inputConfig = array_replace_recursive(
                            $streetElem,
                            ['children' => [$line => $streetConfigUpdate]]
                        );
                        if ((bool)$streetConfigUpdate['required']) {
                            $inputConfig['validation']['required-entry'] = true;
                        } elseif (isset($inputConfig['validation']['required-entry'])) {
                            unset($inputConfig['validation']['required-entry']);
                        }
                        $streetLinesLayoutUpdate = $inputConfig;
                    }
                } else {
                    $streetLinesLayoutUpdate['children'][$line] = $config;
                }
                $rowLayout = $this->arrayManager->set($streetElemPath, $rowLayout, $streetLinesLayoutUpdate);
            }
        }

        return $rowLayout;
    }
}
