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



namespace Mirasvit\SeoSitemap\Console\Command;

use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SitemapGenerateCommand extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Sitemap\Model\ResourceModel\Sitemap\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var string
     */
    protected $logPath = '/var/log/MapCommandGenerate.log';

    /**
     * Info
     * @var array
     */
    protected $info
        = [
            'info'       => 'Get this information message',
            'all'        => 'Generate all available sitemaps',
            'sitemap_id' => 'Generate individual sitemap by sitemap id' . PHP_EOL
                . 'Id of needed sitemap can be taken from Admin Panel grid in '
                . 'Marketing > Advanced SEO Suite > Site Map',
        ];

    /**
     * @param State                                                          $appState
     * @param ObjectManagerInterface                                         $objectManager
     * @param \Magento\Sitemap\Model\ResourceModel\Sitemap\CollectionFactory $collectionFactory
     */
    public function __construct(
        State $appState,
        ObjectManagerInterface $objectManager,
        \Magento\Sitemap\Model\ResourceModel\Sitemap\CollectionFactory $collectionFactory
    ) {
        $this->appState          = $appState;
        $this->objectManager     = $objectManager;
        $this->collectionFactory = $collectionFactory;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:seositemap:generate')
            ->setDescription('Generate all available sitemaps');

        $this->addOption('info', null, null, $this->info['info']);
        $this->addOption('sitemap_id', null, InputOption::VALUE_OPTIONAL, 'Generate individual sitemap by sitemap id');
        $this->addOption('all', null, null, 'Generate all available sitemaps');

        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode('adminhtml');
        } catch (\Exception $e) {
        }

        if ($input->getOption('info')) {
            $output->writeln('php bin/magento mirasvit:seositemap:generate --info   ' . $this->info['info']);
            $output->writeln('php bin/magento mirasvit:seositemap:generate --all   ' . $this->info['all']);
            $output->writeln('php bin/magento mirasvit:seositemap:generate --sitemap_id   ' . $this->info['sitemap_id']);
        }

        if ($input->getOption('all')) {

            $errors = [];

            $output->writeln('Generating available sitemaps. This may take a while');
            $collection = $this->collectionFactory->create();

            $progressBarAll = new ProgressBar($output, count($collection));
            $progressBarAll->setFormat('debug');
            $progressBarAll->start();
            /* @var $collection \Magento\Sitemap\Model\ResourceModel\Sitemap\Collection */
            foreach ($collection as $sitemap) {
                /* @var $sitemap \Magento\Sitemap\Model\Sitemap */
                try {
                    $sitemap->generateXml();
                    $progressBarAll->advance();
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }
            if ($errors) {
                print_r($errors);
            }
            $progressBarAll->finish();
            $output->writeln('Finished generation process');
        }

        if ($sitemapId = $input->getOption('sitemap_id')) {

            $output->writeln('Generating sitemap with ID ' . $sitemapId . '. This may take a while');

            // init and load sitemap model
            $sitemap = $this->objectManager->create('Magento\Sitemap\Model\Sitemap');
            /* @var $sitemap \Magento\Sitemap\Model\Sitemap */
            $sitemap->load($sitemapId);
            // if sitemap record exists
            if ($sitemap->getId()) {
                try {
                    $sitemap->generateXml();

                    $output->writeln('The sitemap ' . $sitemap->getSitemapFilename() . ' has been generated.');
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $output->writeln($e->getMessage());
                }
            } else {
                $output->writeln('We can\'t find a sitemap to generate.');
            }

            $output->writeln('Finished generation process');
        }

    }

    /**
     * Tool for debugging purposes
     *
     * @param string $info
     *
     * @return void
     */
    protected function log($info)
    {
//        $writer = new \Zend\Log\Writer\Stream(BP . $this->logPath);
//        $logger = new \Zend\Log\Logger();
//        $logger->addWriter($writer);
//        $logger->info($info);
    }

}
