<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Console\Command;

/**
 * $ bin/magento help wyomind:simplegoogleshopping:generate
 * Usage:
 * wyomind:simplegoogleshopping:run [feed_id1] ... [feed_idN]
 *
 * Arguments:
 * feed_id            Space-separated list of feeds (generate all feeds if empty)
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
class Generate extends \Symfony\Component\Console\Command\Command
{
    const FEED_ID_ARG = 'feed_id';

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
        $this->setName('wyomind:simplegoogleshopping:generate')
            ->setDescription(__('Simple Google Shopping : generate data feeds'))
            ->setDefinition([
                new \Symfony\Component\Console\Input\InputArgument(
                    self::FEED_ID_ARG,
                    \Symfony\Component\Console\Input\InputArgument::OPTIONAL | \Symfony\Component\Console\Input\InputArgument::IS_ARRAY,
                    __('Space-separated list of data feed (generate all feeds if empty)')
                )
            ]);
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
            $feedsIds = $input->getArgument(self::FEED_ID_ARG);
            $collection = $this->feedsCollectionFactory->create()->getList($feedsIds);
            $first = true;
            foreach ($collection as $feed) {
                $feed->isCron = true;
                if ($first) {
                    $feed->loadCustomFunctions();
                    $first = false;
                }
                $output->write("Generating feed #" . $feed->getId());
                $feed->generateXml();
                $output->writeln(" => generated");
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $output->writeln($e->getMessage());
            $returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        $returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;

        return $returnValue;
    }
}
