<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    AdvancedReports
 * @version    2.8.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */























































































































































































































namespace Aheadworks\AdvancedReports\Model\Config\Initial;

use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\Config\CacheInterface;
use Magento\Framework\App\Config\Initial\Converter;
use Magento\Framework\Config\Dom;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Exploder
 * @package Aheadworks\OneStepCheckout\Model\Config\Initial
 */
class Exploder
{
    /**
     * Cache ID prefix value
     */
    const CACHE_ID_PREFIX = 'aw_arep_initial_config_exploder';

    /**
     * XML config file name
     */
    const FILE_NAME = 'config.xml';

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var FileResolverInterface
     */
    private $fileListResolver;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var ValidationStateInterface
     */
    private $validationState;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @param CacheInterface $cache
     * @param FileResolverInterface $fileListResolver
     * @param Converter $converter
     * @param ValidationStateInterface $validationState
     * @param JsonSerializer $serializer
     */
    public function __construct(
        CacheInterface $cache,
        FileResolverInterface $fileListResolver,
        Converter $converter,
        ValidationStateInterface $validationState,
        JsonSerializer $serializer
    ) {
        $this->cache = $cache;
        $this->fileListResolver = $fileListResolver;
        $this->converter = $converter;
        $this->validationState = $validationState;
        $this->serializer = $serializer;
    }

    /**
     * Retrieve config value by path and explode its value bypassing core merge logic
     *
     * @param string $path
     * @return array
     * @throws LocalizedException
     */
    public function explodeByPath($path)
    {
        $cacheId = self::CACHE_ID_PREFIX . '-' . $path;
        $data = $this->cache->load($cacheId);
        if ($data === false) {
            $data = $this->read($path);
            $this->cache->save($this->serializer->serialize($data), $cacheId);
        } else {
            $data = $this->serializer->unserialize($data);
        }
        return $data;
    }

    /**
     * Read data
     *
     * @param string $path
     * @return array
     * @throws LocalizedException
     */
    private function read($path)
    {
        $result = [];
        $pathParts = explode('/', $path);
        $fileList = $this->fileListResolver->get(self::FILE_NAME, null);
        foreach ($fileList as $key => $content) {
            try {
                $dom = new Dom($content, $this->validationState);
            } catch (ValidationException $e) {
                throw new LocalizedException(__('Invalid XML in file %1:\n%2', $key, $e->getMessage()));
            }

            $output = $this->converter->convert($dom->getDom());
            if (isset($output['data']['default'])) {
                $data = $this->getDataByPathParts($output['data']['default'], $pathParts);
                if ($data !== null) {
                    $result[] = $data;
                }
            }
        }
        return $result;
    }

    /**
     * Get data by path parts
     *
     * @param array $data
     * @param array $pathParts
     * @return mixed|null
     */
    private function getDataByPathParts($data, $pathParts)
    {
        foreach ($pathParts as $key) {
            if ((array)$data === $data && isset($data[$key])) {
                $data = $data[$key];
            } elseif ($data instanceof DataObject) {
                $data = $data->getDataByKey($key);
            } else {
                return null;
            }
        }
        return $data;
    }
}
