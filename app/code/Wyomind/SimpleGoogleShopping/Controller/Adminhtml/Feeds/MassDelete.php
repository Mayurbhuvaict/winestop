<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Controller\Adminhtml\Feeds;

class MassDelete extends \Wyomind\SimpleGoogleShopping\Controller\Adminhtml\Feeds
{
    /**
     * Authorization level
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Wyomind_SimpleGoogleShopping::delete';

    /**
     * Delete action
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->feedsCollectionFactory->create());
        $size = $collection->getSize();

        foreach ($collection as $item) {
            $item->delete();
        }

        $this->messageManager->addSuccess(__('A total of %1 feeds have been deleted', $size));
        return $this->resultRedirectFactory->create()->setPath('simplegoogleshopping/feeds/index');
    }
}
