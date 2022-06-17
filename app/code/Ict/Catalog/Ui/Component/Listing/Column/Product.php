<?php 

namespace Ict\Catalog\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Catalog\Api\ProductRepositoryInterface;


class Product extends Column{
    protected $escaper;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductRepositoryInterface $productRepository,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->escaper = $escaper;
        $this->productRepository = $productRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource){
        $obManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['name'])) {
                    $replaceSymbols =  str_split( \Magento\Framework\Search\Adapter\Mysql\Query\Builder\Match::SPECIAL_CHARACTERS, 1);
                    $html =  htmlspecialchars_decode($item['name'], ENT_QUOTES);
                    $item[$fieldName] = $html;
                }
            }
        }
        return $dataSource;
    }
}
