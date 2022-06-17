<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Controller\Adminhtml\Feeds;

class InlineEdit extends \Wyomind\SimpleGoogleShopping\Controller\Adminhtml\Feeds
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Wyomind_SimpleGoogleShopping::edit';

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $feeds = $this->getRequest()->getParam('items', []);

        if (!($this->getRequest()->getParam('isAjax') && count($feeds))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        $ids = array_keys($feeds);
        $messages = [];

        foreach ($ids as $id) {
            $feed = $this->_objectManager->create('Wyomind\SimpleGoogleShopping\Model\Feeds');
            $feed->load($id);

            try {
                $feed->addData($feeds[$id]);
                $feed->save();
            } catch (\Exception $e) {
                $messages[] = $e->getMessage();
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => count($messages)
        ]);
    }
}
