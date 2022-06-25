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
 * @package   mirasvit/module-core
 * @version   1.3.3
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Core\Ui\QuickDataBar;

abstract class ScalarDataBlock extends AbstractDataBlock
{
    protected $_template = 'Mirasvit_Core::quickDataBar/scalarData.phtml';

    public abstract function getScalarValue(): string;

    public function toArray(array $keys = []): array
    {
        return array_merge(
            parent::toArray(),
            ['value' => $this->getScalarValue()]
        );
    }
}
