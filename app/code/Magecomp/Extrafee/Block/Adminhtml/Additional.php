<?php
namespace Magecomp\Extrafee\Block\Adminhtml;
 
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
 
/**
 * Class Additional
 */
class Additional extends AbstractFieldArray
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @var \Magecomp\Extrafee\Model\CategoryFactory,
     */
    protected $_categoryFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param \Magento\Framework\View\Design\Theme\LabelFactory $labelFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Magecomp\Extrafee\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context, $data);
    }
    /**
     * {@inheritdoc}
     */
    protected $methodOption;

    protected function _prepareToRender()
    {
        $this->addColumn('method_id', ['label' => __('Method'), 'class' => '']);
        $this->addColumn('additional_fee', ['label' => __('Additional Fee'), 'class' => '']);
        
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Additional');
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName == 'method_id' && isset($this->_columns[$columnName])) {
            $colletion = $this->_categoryFactory->create()->getCollection();
            $options=[];
            foreach ($colletion as $key => $_row) {
                $options[]=['value'=>$_row->getId(),'label'=>$_row->getCategoryName()];
            }

            $element = $this->_elementFactory->create('select');
            $element->setForm(
                $this->getForm()
            )->setName(
                $this->_getCellInputElementName($columnName)
            )->setHtmlId(
                $this->_getCellInputElementId('<%- _id %>', $columnName)
            )->setValues(
                $options
            );
            return str_replace("\n", '', $element->getElementHtml());
        }

        return parent::renderCellTemplate($columnName);
    }
}