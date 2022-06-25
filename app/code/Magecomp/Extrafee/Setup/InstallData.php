<?php

namespace Magecomp\Extrafee\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Config factory
     * @var \Magento\Config\Model\Config\Factory
     */
    private $configFactory;

    /**
     * Init
     *
     * @param \Magento\Config\Model\Config\Factory $configFactory
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Magento\Config\Model\Config\Factory $configFactory,
        \Magento\Framework\App\State $state
    )
    {
        $this->configFactory = $configFactory;
        $state->setAreaCode('adminhtml');
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $configData = [
            'section' => 'Extrafee',
            'website' => null,
            'store'   => null,
            'groups'  => [
                'Extrafee' => [
                    'fields' => [
                        'less_one_liter' => [
                            'value' => array(
                                microtime().'_0' => array("box" => "1", "fee" => "2"),
                                microtime().'_1' => array("box" => "2", "fee" => "3"),
                                microtime().'_2' => array("box" => "3", "fee" => "3.5"),
                                microtime().'_3' => array("box" => "4-6", "fee" => "5.8"),
                                microtime().'_4' => array("box" => "6-12", "fee" => "7.5")
                            ),
                        ],
                        'less_two_liter' => [
                            'value' => array(
                                microtime().'_0' => array("box" => "1", "fee" => "4"),
                                microtime().'_1' => array("box" => "2", "fee" => "5"),
                                microtime().'_2' => array("box" => "3", "fee" => "7")
                            ),
                        ],
                    ],
                ],
            ],
        ];

        /** @var \Magento\Config\Model\Config $configModel */
        $configModel = $this->configFactory->create(['data' => $configData]);
        $configModel->save();
    }

}