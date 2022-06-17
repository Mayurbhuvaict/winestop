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

namespace Rootways\Faq\Block\Adminhtml\Faq\Edit\Tab;

class General extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
	protected $_systemStore;
    protected $_status;
    protected $_yesNo;
    protected $_category;
    protected $_coreRegistry;
	protected $_wysiwygConfig;

    public function __construct(
       	\Rootways\Faq\Model\Config\Category $category,
		\Magento\Store\Model\System\Store $systemStore,
        \Rootways\Faq\Model\Config\Yesno $yesNo,
        \Rootways\Faq\Model\Config\IsActive $status,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_category = $category;
		$this->_systemStore = $systemStore;
        $this->_yesNo    = $yesNo;
        $this->_status   = $status;
		$this->_wysiwygConfig = $wysiwygConfig;
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
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Information')]);
        $this->_addElementTypes($fieldset);
		$formData = $this->_coreRegistry->registry('rootways_faq');
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
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true
            ]
        );
		
		$fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'required' => true,
                'config'    => $this->_wysiwygConfig->getConfig()
            ]
        );

        $fieldset->addField(
            'ismostfrequently',
            'select',
            [
                'name'   => 'ismostfrequently',
                'label'  => __('Most Frequent'),
                'title'  => __('Most Frequent'),
                'values' => $this->_yesNo->getYesnoOptions()
            ]
        );
			
		
        $fieldset->addField(
            'cat_id',
            'select',
            [
                'name'   => 'cat_id',
                'label'  => __('Category'),
                'title'  => __('Category'),
                'values' => $this->_category->getCategoryOptions(),
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
                'name'  => 'sortorder',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'size'  => '10'
            ]
        );
		
        if ($formData) {
            if ($formData->getFaqId()) {
                $fieldset->addField(
                    'faq_id',
                    'hidden',
                    ['name' => 'faq_id']
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
