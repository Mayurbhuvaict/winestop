<?php
/**
 * FAQ UI file for collect data.
 *
 * @category  Personalization & Experience Management
 * @package   Rootways_Faq
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2017 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Cusom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
*/

namespace Rootways\Faq\Ui\Component\Listing\Grid\Column;

use \Rootways\Faq\Model\ResourceModel\Faq as faqResourceModel;

class FaqActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    const FAQ_URL_PATH_EDIT = 'rootways/faq/edit';
    const FAQ_URL_PATH_DELETE = 'rootways/faq/delete';
	
    protected $_urlBuilder;
    protected $_actionUrlBuilder;
	
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder $actionUrlBuilder,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_actionUrlBuilder = $actionUrlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
	
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['faq_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->_urlBuilder->getUrl(self::FAQ_URL_PATH_EDIT, ['faq_id' => $item['faq_id']]),
                        'label' => __('Edit')
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->_urlBuilder->getUrl(self::FAQ_URL_PATH_DELETE, ['faq_id' => $item['faq_id']]),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete "${ $.$data.title }"'),
                            'message' => __('Are you sure you want to delete this question?')
                        ]
                    ];
                }

                if (isset($item['identifier'])) {
                    $item[$name]['preview'] = [
                        'href' => $this->_actionUrlBuilder->getUrl(faqResourceModel::FAQ_QUESTION_PATH.'/'.$item['identifier'].faqResourceModel::FAQ_DOT_HTML, isset($item['store_id']) ? $item['store_id'] : null, null),
                        'label' => __('View')
                    ];
                }
            }
        }
        return $dataSource;
    }
}
