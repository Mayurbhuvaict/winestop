<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Console\Command;

/**
 * $ bin/magento help wyomind:simplegoogleshopping:list
 * Usage:
 * wyomind:simplegoogleshopping:list
 *
 * Options:
 * --help (-h)           Display this help message
 * --quiet (-q)          Do not output any message
 * --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 * --version (-V)        Display this application version
 * --ansi                Force ANSI output
 * --no-ansi             Disable ANSI output
 * --no-interaction (-n) Do not ask any interactive question
 */
class Listing extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Framework\App\State|null
     */
    protected $state = null;

    /**
     * @var \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\CollectionFactory|null
     */
    protected $feedsCollectionFactory = null;

    /**
     * @param \Magento\Framework\App\State $state
     * @param \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\CollectionFactory $feedsCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\CollectionFactory $feedsCollectionFactory
    ) {
    
        $this->state = $state;
        $this->feedsCollectionFactory = $feedsCollectionFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('wyomind:simplegoogleshopping:list')
            ->setDescription(__('Simple Google Shopping : get list of available feeds'))
            ->setDefinition([]);
        parent::configure();
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
    
        try {
            $this->state->setAreaCode('adminhtml');
        } catch (\Exception $e) {
        }
        try {
            $collection = $this->feedsCollectionFactory->create();

            $table = new \Symfony\Component\Console\Helper\Table($output);
            $table->setHeaders(['ID', 'File', 'Last generation']);

            foreach ($collection as $feed) {
                $table->addRow([
                    $feed->getSimplegoogleshoppingId(),
                    $feed->getSimplegoogleshoppingPath() . $feed->getSimplegoogleshoppingFilename() . '.xml',
                    $feed->getSimplegoogleshoppingTime()
                ]);
            }

            $table->render($output);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $output->writeln($e->getMessage());
            $returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        $returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;

        return $returnValue;
    }
}
