<?php


/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Framework\Console\Command\License;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\Inputoption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Activate
 * @package Wyomind\Framework\Console\Command\License
 */
class Activate extends \Wyomind\Framework\Console\Command\LicenseAbstract
{

    /**
     *
     */
    public function configure()
    {
        $this->setName('wyomind:license:activate')
            ->setDescription(__('Activate the license for an Wyomind module'))
            ->setDefinition([
                new InputArgument(
                    "module",
                    InputArgument::REQUIRED,
                    __('The module for which you want to activate the license (eg: Wyomind_Framework). 
                    Can be a list of modules separated with a comma.')
                ),
                new InputArgument(
                    "activation-key",
                    InputArgument::OPTIONAL,
                    __('The activation key to use to activate the license. 
                    If <module> is a list of modules, <activation-key> must be a list of activation keys separated with a comma.')
                ),

                new Inputoption(
                    "auto-request",
                    "r",
                    Inputoption::VALUE_NONE,
                    __('Automatically send a license request')
                )
            ]);
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->create();
        $returnValue = \Magento\Framework\Console\Cli::RETURN_SUCCESS;

        try {
            $this->state->setAreaCode('adminhtml');
        } catch (\Exception $e) {
        }


        $list = $this->license->getModuleList();
        $modules = $input->getArgument("module");
        $aks = $input->getArgument("activation-key");
        $autoRequest = $input->getOption("auto-request");

        if ($modules === "all") {
            foreach ($list as $info) {
                $this->activate($info["name"], $input, $output);
            }
        } else {
            $modules = explode(',', $modules);
            $aks = explode(',', $aks);
            if (count($modules) != count($aks)) {
                $message = __("The number of modules doesn't match the number of activation keys");
                throw new \Exception(__("The activation key cannot be empty"));
            }
            $index = 0;
            foreach ($modules as $module) {
                $found = false;
                foreach ($list as $info) {
                    if ($module === $info["name"] || "Wyomind_".$module === $info["name"]) {
                        $found = true;
                        break;
                    }
                }


                if (!$found) {
                    $message = __("The module %1 cannot be found", $module);
                    $message .= "\n" . __("Available modules are:");
                    foreach ($list as $info) {
                        $message .= "\n  - " . $info['name'];
                    }
                    throw new \Exception($message);
                }
                $ak = $aks[$index++];
                if (empty($ak)) {
                    throw new \Exception(__("The activation key cannot be empty"));
                }

                if (strpos($module, "Wyomind_") !== 0) {
                    $module = "Wyomind_".$module;
                }

                $this->activate($module, $input, $output, $ak, $autoRequest);
            }
        }
        return $returnValue;
    }
}
