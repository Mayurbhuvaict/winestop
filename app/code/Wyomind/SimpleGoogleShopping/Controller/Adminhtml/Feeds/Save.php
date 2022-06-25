<?php
/**
 * Copyright © 2020 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Controller\Adminhtml\Feeds;

/**
 * Save data feed action
 */
class Save extends \Wyomind\SimpleGoogleShopping\Controller\Adminhtml\Feeds
{
    /**
     * Authorization level
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Wyomind_SimpleGoogleShopping::edit';

    /**
     * Execute action
     * @return void
     */
    public function execute()
    {
        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {
            $model = $this->_objectManager->create('Wyomind\SimpleGoogleShopping\Model\Feeds');

            $id = $this->getRequest()->getParam('simplegoogleshopping_id');

            if ($id) {
                $model->load($id);
            }

            $toSanitize = [
                'simplegoogleshopping_filename',
                'simplegoogleshopping_name',
                'simplegoogleshopping_path',
                'simplegoogleshopping_url',
                'simplegoogleshopping_title',
                'simplegoogleshopping_description',
                'simplegoogleshopping_note'
            ];

            $allowedToEditScripts = $this->_authorization->isAllowed('Wyomind_SimpleGoogleShopping::edit_scripts');

            foreach ($data as $index => $value) {
                if (in_array($index, $toSanitize)) {
                    $value = $this->sgsHelper->stripTagsContent($value);
                }

                // Check if PHP scripts have been updated - ACL Wyomind_SimpleGoogleShopping::edit_scripts
                if ($allowedToEditScripts === false) {
                    $updateAllowed = true;

                    // XML pattern
                    if ($index === 'simplegoogleshopping_xmlitempattern') {
                        // PHP scripts in the XML pattern from the database
                        $xlmPattern = $model->getSimplegoogleshoppingXmlitempattern();
                        $historicScripts = [];
                        preg_match_all("/(?<script><\?php(?<php>.*)\?>)/sU", $xlmPattern, $historicScripts);

                        // PHP scripts in the XML pattern from the sent post data
                        $currentScripts = [];
                        preg_match_all("/(?<script><\?php(?<php>.*)\?>)/sU", $value, $currentScripts);

                        // Compare data
                        if (count($historicScripts['php']) === count($currentScripts['php'])) {
                            if (count($historicScripts['php']) > 0 || count($currentScripts['php']) > 0) {
                                if (array_diff_assoc($historicScripts['php'], $currentScripts['php'])) {
                                    $updateAllowed = false;
                                }
                            }
                        } else {
                            $updateAllowed = false;
                        }
                    }

                    // Advanced filters
                    if (true === $updateAllowed && $index === 'simplegoogleshopping_attributes') {
                        $historicFilterScripts = [];
                        $currentFilterScripts = [];

                        // PHP scripts in Advanced filters from the database
                        $advancedFilters = $model->getSimplegoogleshoppingAttributes();
                        $historicMatches = [];
                        preg_match_all("/(?<script><\?php(?<php>.*)\?>)/sU", $advancedFilters, $historicMatches);

                        foreach (array_values($historicMatches['php']) as $phpCode) {
                            $historicFilterScripts[] = $phpCode;
                        }

                        // PHP scripts in Advanced filters from the sent post data
                        $matches = [];
                        preg_match_all("/(?<script><\?php(?<php>.*)\?>)/sU", $value, $matches);

                        foreach (array_values($matches['php']) as $phpCode) {
                            $currentFilterScripts[] = $phpCode;
                        }

                        // Compare data
                        if (count($historicFilterScripts) === count($currentFilterScripts)) {
                            if (count($historicFilterScripts) > 0 || count($currentFilterScripts) > 0) {
                                if (array_diff_assoc($historicFilterScripts, $currentFilterScripts)) {
                                    $updateAllowed = false;
                                }
                            }
                        } else {
                            $updateAllowed = false;
                        }
                    }

                    if (false === $updateAllowed) {
                        $this->messageManager->addError(__('Unable to save the data feed: you are not allowed to update the PHP scripts'));
                        return $this->resultRedirectFactory->create()->setPath('simplegoogleshopping/feeds/edit', ['id' => $model->getId(), "_current" => true]);
                    }
                }

                $model->setData($index, $value);
            }

            if (!$this->_validatePostData($data)) {
                return $this->resultRedirectFactory->create()->setPath('simplegoogleshopping/feeds/edit', ['id' => $model->getId(), "_current" => true]);
            }

            try {
                $model->openDestinationFile(false);
                $model->save();

                $this->messageManager->addSuccess(__('The data feed has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                if ($this->getRequest()->getParam('back_i') === "1") {
                    return $this->resultRedirectFactory->create()->setPath('simplegoogleshopping/feeds/edit', ['id' => $model->getId(), "_current" => true]);
                }

                if ($this->getRequest()->getParam('generate_i') === "1") {
                    $this->getRequest()->setParam('simplegoogleshopping_id', $model->getId());
                    return $this->resultForwardFactory->create()->forward("generate");
                }

                $this->_getSession()->setFormData($data);
                return $this->resultRedirectFactory->create()->setPath('simplegoogleshopping/feeds/index');
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Unable to save the data feed.') . '<br/><br/>' . $e->getMessage());
                return $this->resultRedirectFactory->create()->setPath('simplegoogleshopping/feeds/edit', ['id' => $model->getId(), "_current" => true]);
            }
        }

        return $this->resultRedirectFactory->create()->setPath('simplegoogleshopping/feeds/index');
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function _validatePostData($data)
    {
        $errorNo = true;
        if (!empty($data['layout_update_xml']) || !empty($data['custom_layout_update_xml'])) {
            $validatorCustomLayout = $this->_objectManager->create('Magento\Core\Model\Layout\Update\Validator');
            if (!empty($data['layout_update_xml']) && !$validatorCustomLayout->isValid($data['layout_update_xml'])) {
                $errorNo = false;
            }
            if (!empty($data['custom_layout_update_xml']) && !$validatorCustomLayout->isValid(
                $data['custom_layout_update_xml']
            )
            ) {
                $errorNo = false;
            }
            foreach ($validatorCustomLayout->getMessages() as $message) {
                $this->messageManager->addError($message);
            }
        }

        return $errorNo;
    }
}
