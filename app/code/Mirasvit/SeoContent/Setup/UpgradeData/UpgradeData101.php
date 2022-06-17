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



namespace Mirasvit\SeoContent\Setup\UpgradeData;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Mirasvit\SeoContent\Api\Data\TemplateInterface;
use Mirasvit\SeoContent\Api\Repository\TemplateRepositoryInterface;
use Mirasvit\Core\Service\SerializeService;

class UpgradeData101 implements UpgradeDataInterface
{
    /**
     * @var TemplateRepositoryInterface
     */
    private $templateRepository;

    /**
     * UpgradeData101 constructor.
     * @param TemplateRepositoryInterface $templateRepository
     */
    public function __construct(
        TemplateRepositoryInterface $templateRepository
    ) {
        $this->templateRepository = $templateRepository;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Json_Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        foreach ($this->templateRepository->getCollection() as $template) {
            $conditions = $template->getData(TemplateInterface::CONDITIONS_SERIALIZED);

            try {
                \Zend_Json::decode($conditions);
            } catch (\Exception $e) {
                $decodedConditions = SerializeService::decode($conditions);
                if (!$decodedConditions) {
                    $decodedConditions = [0=>$conditions];
                }
                $conditions = \Zend_Json::encode($decodedConditions);
            }

            $replaces = [
                "Mirasvit\\\\SeoContent\\\\Model\\\\Template\\\\Rule\\\\Condition\\\\Validate" =>
                    "Mirasvit\\\\SeoContent\\\\Model\\\\Template\\\\Rule\\\\Condition\\\\CategoryCondition",

                "Mirasvit\\\\Seo\\\\Model\\\\Template\\\\Rule" =>
                    "Mirasvit\\\\SeoContent\\\\Model\\\\Template\\\\Rule",
            ];

            foreach ($replaces as $from => $to) {
                $conditions = str_replace($from, $to, $conditions);
            }

            $template->setConditionsSerialized($conditions);
            $this->templateRepository->save($template);
        }
    }
}
