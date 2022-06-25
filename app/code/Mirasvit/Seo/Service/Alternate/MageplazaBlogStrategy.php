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

namespace Mirasvit\Seo\Service\Alternate;

use Magento\Framework\App\RequestInterface;

class MageplazaBlogStrategy
{
    private $manager;

    private $url;

    protected $request;
    /**
     * @var mixed
     */
    private $helperBlog;

    public function __construct(
        \Magento\Framework\Module\Manager $manager,
        \Mirasvit\Seo\Api\Service\Alternate\UrlInterface $url,
        RequestInterface $request
    ) {
        $this->manager = $manager;
        $this->url     = $url;
        $this->request = $request;
    }

    public function getStoreUrls(): array
    {
        if ($this->manager->isEnabled('Mageplaza_Blog') && class_exists('\Mageplaza\Blog\Helper\Data')) {
            $this->helperBlog = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Mageplaza\Blog\Helper\Data::class
            );
            $id = $this->getRequest()->getParam('id');
            $post = $this->helperBlog->getFactoryByType(\Mageplaza\Blog\Helper\Data::TYPE_POST)->create()->load($id);
            $storeUrls = $this->url->getStoresCurrentUrl();
            $allowedStores = explode(',', $post->getStoreIds());

            if (!$storeUrls) {
                return [];
            }

            foreach ($storeUrls as $key => $value) {
                if (!in_array($key, $allowedStores)) {
                    unset($storeUrls[$key]);
                }
            }

            return $storeUrls;
        } else {
            return [];
        }
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
