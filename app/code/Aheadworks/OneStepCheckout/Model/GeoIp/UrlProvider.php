<?php
namespace Aheadworks\OneStepCheckout\Model\GeoIp;

use Aheadworks\OneStepCheckout\Model\Config;

/**
 * Class UrlProvider
 * @package Aheadworks\OneStepCheckout\Model\GeoIp
 */
class UrlProvider
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Get download url
     *
     * @return string
     */
    public function getDownloadUrl()
    {
        return 'download.maxmind.com/app/geoip_download?edition_id=GeoLite2-Country&license_key='
            . $this->config->getLicenseKey()
            . '&suffix=tar.gz';
    }
}
