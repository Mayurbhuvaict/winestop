<?php
/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Controller\Feeds;

/**
 * Generate action
 */
class Generate extends \Wyomind\SimpleGoogleShopping\Controller\Feeds
{
    /**
     * Execute action
     */
    public function execute()
    {
        // http://www.example.com/index.php/simplegoogleshopping/feeds/generate/id/{data_feed_id}/ak/{YOUR_ACTIVATION_KEY}

        $id = $this->getRequest()->getParam('id');
        $ak = $this->getRequest()->getParam('ak');

        $activationKey = $this->licenseHelper->getDefaultConfigUncrypted('simplegoogleshopping/license/activation_key');

        $resultRaw = $this->resultRawFactory->create();
        if ($activationKey == $ak) {
            $simplegoogleshopping = $this->_objectManager->create('Wyomind\SimpleGoogleShopping\Model\Feeds');
            $simplegoogleshopping->setId($id);
            if ($simplegoogleshopping->load($id)) {
                try {
                    $simplegoogleshopping->generateXml($this->getRequest());
                    return $resultRaw->setContents(
                        sprintf(__('The data feed ') . "\"" . $simplegoogleshopping->getSimplegoogleshoppingFilename() . "\"" . __(' has been generated.'))
                    );
                } catch (\Exception $e) {
                    return $resultRaw->setContents($e->getMessage());
                }
            } else {
                return $resultRaw->setContents(__('Unable to find a data feed to generate.'));
            }
        } else {
            return $resultRaw->setContents(__('Invalid activation key'));
        }
    }
}
