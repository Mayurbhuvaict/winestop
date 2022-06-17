<?php
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\GeoIp;

use Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\Composer\PackageInfo;
use Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\File\Info;
use Aheadworks\OneStepCheckout\Model\Config;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;

/**
 * Class DownloadDatabase
 *
 * @method string getButtonLabel()
 * @method string getButtonLabelDownloaded()
 * @method string getSubmitPath()
 * @method string getPackageName()
 * @method string getFileName()
 * @method string getArchiveName()
 *
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\GeoIp
 */
class DownloadDatabase extends Field
{
    /**
     * @var PackageInfo
     */
    private $packageInfo;

    /**
     * @var Info
     */
    private $fileInfo;

    /**
     * @var Config
     */
    private $config;

    /**
     * {@inheritdoc}
     */
    protected $_template = 'system/config/geo_ip/download_database.phtml';

    /**
     * @param Context $context
     * @param PackageInfo $packageInfo
     * @param Info $fileInfo
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        PackageInfo $packageInfo,
        Info $fileInfo,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->packageInfo = $packageInfo;
        $this->fileInfo = $fileInfo;
        $this->config = $config;
    }

    /**
     * Check if disabled
     *
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->isLibInstalled();
    }

    /**
     * Get submit url
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl($this->getSubmitPath());
    }

    /**
     * Check if library package installed
     *
     * @return bool
     */
    private function isLibInstalled()
    {
        return $this->packageInfo->isInstalled($this->getPackageName());
    }

    /**
     * Check if downloaded
     *
     * @return bool
     */
    public function isDownloaded()
    {
        return $this->fileInfo->isExist($this->getFileName());
    }

    /**
     * Check is update available
     *
     * @return bool
     */
    public function isUpdateAvailable()
    {
        return (bool)$this->config->getLicenseKey();
    }

    /**
     * Check if downloaded and library package installed
     *
     * @return bool
     */
    public function isDownloadedAndLibInstalled()
    {
        return $this->isDownloaded() && $this->isLibInstalled();
    }

    /**
     * Get last updated datetime
     *
     * @return string
     */
    public function getLastUpdatedAt()
    {
        $timestamp = $this->fileInfo->getModificationTimestamp($this->getFileName());
        return $this->_localeDate->formatDateTime((new \DateTime())->setTimestamp($timestamp));
    }

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData(
            [
                'button_label_downloaded' => $originalData['button_label_downloaded'],
                'submit_path' => $originalData['submit_path'],
                'package_name' => $originalData['package_name'],
                'file_name' => $originalData['file_name'],
                'archive_name' => $originalData['archive_name'],
            ]
        )->addData(
            [
                'button_label' => $this->isDownloaded()
                    ? $originalData['button_label_downloaded']
                    : $originalData['button_label_not_downloaded']
            ]
        );
        return $this->_toHtml();
    }
}
