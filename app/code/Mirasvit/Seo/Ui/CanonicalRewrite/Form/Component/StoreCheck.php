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


namespace Mirasvit\Seo\Ui\CanonicalRewrite\Form\Component;

use Mirasvit\Seo\Api\Repository\CanonicalRewriteRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Seo\Api\Data\CanonicalRewriteInterface;
use Mirasvit\Seo\Api\Data\CanonicalRewriteStoreInterface;

class StoreCheck
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var CanonicalRewriteRepositoryInterface
     */
    private $canonicalRewriteRepository;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Constructor
     *
     * @param RequestInterface $request
     * @param CanonicalRewriteRepositoryInterface $canonicalRewriteRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        RequestInterface $request,
        CanonicalRewriteRepositoryInterface $canonicalRewriteRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->request = $request;
        $this->canonicalRewriteRepository = $canonicalRewriteRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Check if applied for all stores
     *
     * @return bool
     */
    public function isAppliedAllStores()
    {
        if ($id = $this->request->getParam(CanonicalRewriteInterface::ID_ALIAS)) {
            return $this->isAllStoresSelected($id);
        } elseif (($stores = $this->storeManager->getStores())
            && is_array($stores)
            && count($stores) < 2) {
                return true;
        }

        return false;
    }

    /**
     * Check if all stores (store_id = 0)
     *
     * @param int $id
     * @return bool
     */
    protected function isAllStoresSelected($id)
    {
        $item = $this->canonicalRewriteRepository
            ->getCollection()
            ->addStoreColumn()
            ->addFieldToFilter(CanonicalRewriteInterface::ID, $id)
            ->getFirstItem();

        $storeIds = $item->getData(CanonicalRewriteStoreInterface::STORE_ID);
        if ((is_array($storeIds) && count($storeIds) == 1
                && isset($storeIds[0]) && $storeIds[0] == 0)
            || ($storeIds == 0)) {
            return true;
        }

        return false;
    }
}
