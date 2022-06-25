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


namespace Mirasvit\SeoAudit\Model\Config\Source;


use Magento\Framework\Option\ArrayInterface;
use Mirasvit\SeoAudit\Repository\CheckResultRepository;

class Check implements ArrayInterface
{
    private $repository;

    public function __construct(CheckResultRepository $repository)
    {
        $this->repository = $repository;
    }

    public function toOptionArray(): array
    {
        $options = [];

        foreach ($this->repository->getAllChecks() as $check) {
            $options[] = [
                'label' => $check->getLabel(),
                'value' => $check->getIdentifier()
            ];
        }

        return $options;
    }

    public function getLabel(string $identifier): ?string
    {
        foreach ($this->toOptionArray() as $item) {
            if ($item['value'] == $identifier) {
                return $item['label'];
            }
        }

        return null;
    }
}
