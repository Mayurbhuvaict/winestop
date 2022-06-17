<?php

namespace Meetanshi\ShippingRates\Block\Adminhtml\Method\Edit\Tab;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store as SystemStore;
use Magento\Framework\View\Asset\Repository;

class Import extends Form
{
    protected $systemStore;
    protected $formFactory;
    protected $registry;
    protected $context;
    protected $assetRepo;

    public function __construct(SystemStore $systemStore, FormFactory $formFactory, Registry $registry, Context $context, Repository $assetRepo)
    {
        $this->systemStore = $systemStore;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->assetRepo = $assetRepo;
        parent::__construct($context);
    }

    protected function _prepareForm()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);

        $fieldSet = $form->addFieldset('shippingrates_import', ['legend' => __('Import Rates')]);
        $fieldSet->addField('import_clear', 'select', [
            'label' => __('Delete Existing Rates'),
            'name' => 'import_clear',
            'values' => [
                [
                    'value' => 0,
                    'label' => __('No')
                ],
                [
                    'value' => 1,
                    'label' => __('Yes')
                ]]
        ]);

        $csvFile = $this->assetRepo->getUrl('Meetanshi_ShippingRates::csv/rates.csv');
        $csvLink = "<a href=" . $csvFile . " target='_blank'>Download Sample File</a>";

        $fieldSet->addField('import_file', 'file', [
            'label' => __('CSV File'),
            'name' => 'import_file',
            'note' => $csvLink
        ]);

        return parent::_prepareForm();
    }
}
