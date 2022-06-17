<?php
/**
 * Block for get information of FAQ.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
*/

namespace Rootways\Faq\Block\Adminhtml\Faqcat\Edit\Tab;

class General extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_status;
    protected $_yesNo;
    protected $_systemStore;
    protected $_coreRegistry;

    public function __construct(
        \Rootways\Faq\Model\Config\Yesno $yesNo,
        \Rootways\Faq\Model\Config\IsActive $status,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->_yesNo = $yesNo;
        $this->_status = $status;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }
	
    protected function _construct()
    {
        parent::_construct();
        $this->setActive(true);
    }
	
    public function getTabLabel()
    {
        return __('General Information');
    }
	
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }
	
    public function canShowTab()
    {
        return true;
    }
	
    public function isHidden()
    {
        return false;
    }
	
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Category Information')]);
        $this->_addElementTypes($fieldset);

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'values' => $this->_status->getStatusOptions()
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true
            ]
        );
        
        if (!$this->_storeManager->hasSingleStore()) {
            $field = $fieldset->addField(
                'stores',
                'multiselect',
                [
                    'label' => __('Stores View'),
                    'title' => __('Stores View'),
                    'required' => true,
                    'name' => 'stores[]',
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true)
                ]
            );
        } else {
            $fieldset->addField(
                'stores',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
			$formData['stores'] = $this->_storeManager->getStore(true)->getId();
            //$formData->setSelectStores($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField(
            'sortorder',
            'text',
            [
                'name' => 'sortorder',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'size' => '10'
            ]
        );

        $formData = $this->_coreRegistry->registry('rootways_faqcat');
        if ($formData) {
            if ($formData->getCatId()) {
                $fieldset->addField(
                    'cat_id',
                    'hidden',
                    ['name' => 'cat_id']
                );
            }
            if ($formData->getIsActive() == null) {
                $formData->setIsActive('1');
            }
            $form->setValues($formData->getData());
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
