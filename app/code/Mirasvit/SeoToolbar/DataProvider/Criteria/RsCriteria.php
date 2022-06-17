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



namespace Mirasvit\SeoToolbar\DataProvider\Criteria;

use Magento\Framework\App\RequestInterface;
use Mirasvit\SeoToolbar\Api\Data\DataProviderItemInterface;

class RsCriteria extends AbstractCriteria
{
    const LABEL = 'Rich Snippets';

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * RsCriteria constructor.
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param string $content
     * @return \Magento\Framework\DataObject
     */
    public function handle($content)
    {
        $validateUrl = 'https://search.google.com/structured-data/testing-tool#url=' . $this->request->getUriString();

        return $this->getItem(
            self::LABEL,
            DataProviderItemInterface::STATUS_NONE,
            '',
            '',
            '<a target="_blank" rel="nofollow" href="' . $validateUrl . '">Validate</a>'
        );
    }
}
