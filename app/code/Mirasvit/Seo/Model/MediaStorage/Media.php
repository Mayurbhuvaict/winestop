<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.3.10
 * @copyright Copyright (C) 2022 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Seo\Model\MediaStorage;

use Magento\Catalog\Model\View\Asset\PlaceholderFactory;
use Magento\Framework\App;
use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\State;
use Magento\Framework\AppInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\MediaStorage\Model\File\Storage\Config;
use Magento\MediaStorage\Model\File\Storage\ConfigFactory;
use Magento\MediaStorage\Model\File\Storage\Response;
use Magento\MediaStorage\Model\File\Storage\Synchronization;
use Magento\MediaStorage\Model\File\Storage\SynchronizationFactory;
use Magento\MediaStorage\Service\ImageResize;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Media implements AppInterface
{
    /**
     * Authorization function
     */
    private $isAllowed;

    /**
     * Media directory path
     */
    private $mediaDirectoryPath;

    /**
     * Configuration cache file path
     */
    private $configCacheFile;

    /**
     * Requested file name relative to working directory
     */
    private $relativeFileName;

    private $response;

    private $directoryPub;

    private $directoryMedia;

    private $configFactory;

    private $syncFactory;

    private $placeholderFactory;

    private $appState;

    private $imageResize;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ConfigFactory $configFactory,
        SynchronizationFactory $syncFactory,
        Response $response,
        \Closure $isAllowed,
        Filesystem $filesystem,
        PlaceholderFactory $placeholderFactory,
        State $state,
        ImageResize $imageResize,
        File $file,
        string $relativeFileName = null,
        string $configCacheFile = null,
        string $mediaDirectory = null
    ) {
        $this->response       = $response;
        $this->isAllowed      = $isAllowed;
        $this->directoryPub   = $filesystem->getDirectoryWrite(DirectoryList::PUB);
        $this->directoryMedia = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $mediaDirectory       = trim((string)$mediaDirectory);
        
        if (!empty($mediaDirectory)) {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $this->mediaDirectoryPath = str_replace('\\', '/', $file->getRealPath($mediaDirectory));
        }
        
        $this->configCacheFile    = $configCacheFile;
        $this->relativeFileName   = $relativeFileName;
        $this->configFactory      = $configFactory;
        $this->syncFactory        = $syncFactory;
        $this->placeholderFactory = $placeholderFactory;
        $this->appState           = $state;
        $this->imageResize        = $imageResize;
    }

    public function launch(): ResponseInterface
    {
        $this->appState->setAreaCode(Area::AREA_GLOBAL);

        if ($this->checkMediaDirectoryChanged()) {
            // Path to media directory changed or absent - update the config
            /** @var Config $config */
            $config = $this->configFactory->create(['cacheFile' => $this->configCacheFile]);
            $config->save();
            
            $this->mediaDirectoryPath = $config->getMediaDirectory();
            
            $allowedResources = $config->getAllowedResources();
            $isAllowed        = $this->isAllowed;
            $fileAbsolutePath = $this->directoryPub->getAbsolutePath($this->relativeFileName);
            $fileRelativePath = str_replace(rtrim($this->mediaDirectoryPath, '/') . '/', '', $fileAbsolutePath);
            
            if (!$isAllowed($fileRelativePath, $allowedResources)) {
                throw new \LogicException('The path is not allowed: ' . $this->relativeFileName);
            }
        }

        try {
            /** @var Synchronization $sync */
            $sync = $this->syncFactory->create(['directory' => $this->directoryPub]);
            $sync->synchronize($this->relativeFileName);
            $this->imageResize->resizeFromImageName($this->getOriginalImage($this->relativeFileName));
            
            if ($this->directoryPub->isReadable($this->relativeFileName)) {
                $this->response->setFilePath($this->directoryPub->getAbsolutePath($this->relativeFileName));
            } else {
                $this->setPlaceholderImage();
            }
        } catch (\Exception $e) {
            $this->setPlaceholderImage();
        }

        return $this->response;
    }

    private function checkMediaDirectoryChanged(): bool
    {
        return rtrim((string)$this->mediaDirectoryPath, '/') !== rtrim((string)$this->directoryMedia->getAbsolutePath(), '/');
    }

    private function setPlaceholderImage(): void
    {
        $placeholder = $this->placeholderFactory->create(['type' => 'image']);
        $this->response->setFilePath($placeholder->getPath());
    }

    private function getOriginalImage(string $resizedImagePath): string
    {
        return preg_replace('|^.*((?:/[^/]+){3})$|', '$1', $resizedImagePath);
    }

    public function catchException(App\Bootstrap $bootstrap, \Exception $exception)
    {
        $this->response->setHttpResponseCode(404);
        
        if ($bootstrap->isDeveloperMode()) {
            $this->response->setHeader('Content-Type', 'text/plain');
            $this->response->setBody($exception->getMessage() . "\n" . $exception->getTraceAsString());
        }
        
        $this->response->sendResponse();
        
        return true;
    }
}
