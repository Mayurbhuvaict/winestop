<?php
namespace Meigee\Barbour\Block\Frontend;
use \Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class CustomDesign extends \Magento\Framework\View\Element\Template
{

	protected $_filesystem;
	protected $configWriter;

	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Filesystem $_filesystem,
		\Magento\Framework\View\Design\Theme\ThemeProviderInterface $themeProvider,
		\Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
		\Magento\Framework\App\Request\Http $request
    ) {
		$this->_scopeConfig = $scopeConfig;
		$this->_storeManager = $storeManager;
		$this->_filesystem = $_filesystem;
		$this->_themeProvider = $themeProvider;
		$this->configWriter = $configWriter;
		$this->_request = $request;
    }

	public function getConfigData($section, $group, $field, $s_id = null) {
		($s_id == null ? $store_id = $this->_request->getParam('store') : $store_id = $s_id);
		($store_id == null ? $store_id = 0 : $store_id = $store_id);

		return $this->_scopeConfig->getValue($section.'/'.$group.'/'.$field, ScopeInterface::SCOPE_STORE, $store_id);
	}

	public function includeCss($storeId) {

		$def_conf = 'barbour_theme_design';
		$configs = array('', '_blue', '_red', '_green', '_navi', '_cloth', '_cloth_orange', '_cloth_yellow', '_cloth_purple', '_electronics1', '_electronics1_red', '_electronics1_green', '_electronics1_black', '_electronics2', '_electronics2_red', '_electronics2_blue', '_electronics2_red2', '_jewelry', '_jewelry_beige', '_jewelry_black', '_jewelry_red', '_perfume', '_perfume_pink', '_perfume_purple', '_perfume_beige', '_handmade', '_handmade_blue', '_handmade_pink', '_handmade_beige', '_cloth_2', '_cloth_2_blue', '_cloth_2_orange', '_cloth_2_green', '_cloth_3', '_cloth_3_red', '_cloth_3_purple', '_cloth_3_purple_2');

		$file = null;

		foreach($configs as $config) {
			$conf_val = $this->getConfigData($def_conf.$config, 'general', 'css_file', $storeId);
			if(!empty($conf_val)/* or $conf_val != null*/){
				$file = $conf_val;
				break 1;
			}
			elseif($storeId = 1){
				$file = $conf_val = $this->getConfigData($def_conf.$config, 'general', 'css_file', 0);
				break 1;
			}
		}

		if($file != null) {
			$css = '<link rel="stylesheet" href="'.$this->getDesignDir(true).$file.'" />';
		}
		 else {
			$css = false;
		}
		return $css;
	}

	public function saveOpt($param = false, $sectionId) {
		if($param == true) {
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$model = $objectManager->create('\Meigee\Barbour\Model\cssGenerate');

			$data = $model->getDesignValues($sectionId);
			$file = $this->createCssFile($data, $sectionId);
			return $file;
		}
	}

	public function getDesignDir($is_link = false) {
		if($is_link) {
			$mediapath = $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		} else {
			$mediapath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
		}
		$dir = 'meigee/';
		return $mediapath.$dir;
	}

	public function createCssFile($data, $sectionId) {
		// $store_id = $this->_storeManager->getStore()->getStoreId();
		$store_code = $this->_storeManager->getStore()->getCode();
		// $store_code = $this->_request->getParam('code');
		$store_id = $this->_request->getParam('store');

		($store_id == null ? $store_id = 0 : $store_id = $store_id);

		$design_dir = $this->getDesignDir();
		$dir_exist = is_dir($design_dir);
		$theme_name = $this->getThemeData();
		$theme_name = $theme_name['theme_title'];
		$theme_name = strtolower($theme_name);
		$file_name = $theme_name.'_'.$store_code.$store_id.'.css';

		if(!$dir_exist) {
			mkdir($design_dir);
		}

		$f = fopen($design_dir.$file_name, 'w+');
		fwrite($f, $data);
		fclose($f);

		$this->configWriter->save(''.$sectionId.'/general/css_file',  $file_name, ScopeInterface::SCOPE_STORES, $store_id);
	}

	public function getThemeData() {
        $themeId = $this->_scopeConfig->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getId()
        );

        $theme = $this->_themeProvider->getThemeById($themeId);
        return $theme->getData();
    }

	public function removeDataByPath($path) {
		$store_id = $this->_request->getParam('store');
		$this->configWriter->delete($path, ScopeInterface::SCOPE_STORES, $store_id);
	}
}
