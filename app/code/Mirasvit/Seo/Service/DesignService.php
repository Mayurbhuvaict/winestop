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



namespace Mirasvit\Seo\Service;

use Mirasvit\Seo\Api\Service\DesignServiceInterface;
use Magento\Framework\View\DesignInterface;

class DesignService implements DesignServiceInterface
{
    /**
     * @var DesignInterface
     */
    private $design;

    /**
     * @param DesignInterface $design
     */
    public function __construct(
        DesignInterface $design
    ) {
        $this->design = $design;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeCode()
    {
        return $this->design->getDesignTheme()->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedThemeForUpdateMeta()
    {
        $allowedTheme = ['Mgs/unero'];
        if (in_array($this->getThemeCode(), $allowedTheme)) {
            return true;
        }

        return false;
    }
}
