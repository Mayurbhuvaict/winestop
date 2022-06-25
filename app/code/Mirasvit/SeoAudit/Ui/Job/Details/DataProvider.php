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


declare(strict_types=1);


namespace Mirasvit\SeoAudit\Ui\Job\Details;


use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\SeoAudit\Api\Data\JobInterface;
use Mirasvit\SeoAudit\Repository\JobRepository;

class DataProvider extends AbstractDataProvider
{
    private $context;

    private $jobRepository;

    public function __construct(
        ContextInterface $context,
        JobRepository $jobRepository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = [])
    {
        $this->context = $context;
        $this->jobRepository = $jobRepository;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        $result = [];

        $model = $this->getModel();

        if ($model) {
            $data = $model->getData();

            $result[$model->getId()] = $data;
        }

        return $result;
    }

    private function getModel(): ?JobInterface
    {
        $id = $this->context->getRequestParam(JobInterface::ID, 0);

        return $id ? $this->jobRepository->get((int)$id) : null;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        return;
    }
}
