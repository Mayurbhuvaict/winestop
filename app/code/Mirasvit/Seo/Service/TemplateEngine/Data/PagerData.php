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

namespace Mirasvit\Seo\Service\TemplateEngine\Data;

use Magento\Framework\App\RequestInterface;

class PagerData extends AbstractData
{
    private $request;

    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;

        parent::__construct();
    }

    public function getTitle(): string
    {
        return (string)__('Pagination');
    }

    public function getVariables(): array
    {
        return [
            'page',
        ];
    }

    public function getValue(string $attribute, array $additionalData = []): ?string
    {
        $page = (int)$this->request->getParam('p');

        switch ($attribute) {
            case 'page':
                return $page > 1 ? (string)__('Page %1', $page) : null;
        }

        return null;
    }
}
