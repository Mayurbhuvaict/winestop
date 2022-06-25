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


namespace Mirasvit\SeoAudit\Console\Command;


use Magento\Framework\App\MaintenanceMode;
use Magento\Framework\App\StateFactory;
use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\SeoAudit\Api\Data\JobInterface;
use Mirasvit\SeoAudit\Model\ConfigProvider;
use Mirasvit\SeoAudit\Repository\JobRepository;
use Mirasvit\SeoAudit\Repository\UrlRepoitory;
use Mirasvit\SeoAudit\Service\CheckResultService;
use Mirasvit\SeoAudit\Service\CheckService;
use Mirasvit\SeoAudit\Service\JobService;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class JobCommand extends \Symfony\Component\Console\Command\Command
{
    private $config;

    private $appStateFactory;

    private $jobService;

    private $jobRepository;

    private $checkService;

    private $checkResultService;

    private $urlRepository;

    private $maintenanceMode;

    public function __construct(
        ConfigProvider $config,
        StateFactory $appStateFactory,
        JobService $jobService,
        JobRepository $jobRepository,
        CheckService $checkService,
        CheckResultService $checkResultServise,
        UrlRepoitory $urlRepoitory,
        MaintenanceMode $maintenanceMode
    ) {
        $this->config             = $config;
        $this->appStateFactory    = $appStateFactory;
        $this->jobService         = $jobService;
        $this->jobRepository      = $jobRepository;
        $this->checkService       = $checkService;
        $this->checkResultService = $checkResultServise;
        $this->urlRepository      = $urlRepoitory;
        $this->maintenanceMode    = $maintenanceMode;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('mirasvit:seo-audit:job')
            ->setDescription('Run SEO audit');

        $this->addOption(
            'start',
            null,
            InputOption::VALUE_NONE,
            'Generate the SEO audit job. All previously generated jobs will be forcibly finished'
        );
        $this->addOption(
            'run',
            null,
            InputOption::VALUE_NONE,
            'Run SEO audit job. The execution has time limitation and limit for URLs to process (100). Run this command repeatedly to process more URLs'
        );
        $this->addOption(
            'stop',
            null,
            InputOption::VALUE_NONE,
            'Finish all running jobs'
        );
        $this->addOption(
            'reset',
            null,
            InputOption::VALUE_NONE,
            'Reset module tables to fresh install state'
        );
        $this->addOption(
            'limit',
            null,
            InputOption::VALUE_REQUIRED,
            'Set the time limit (in seconds) for job to run. Minimum limit is 30 seconds. Maximum limit is 1 hour (3600 seconds)'
        );

        parent::configure();
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $this->appStateFactory->create()->setAreaCode('frontend');
        } catch (\Exception $e) {
        }

        if ($input->getOption('reset')) {
            $q = '<info>All tables of the SeoAudit module will be truncated, all checks and crawled URLs will be lost.</info> Proceed? [y/n]: ';

            /** @var ConfirmationQuestion $question */
            $question = CompatibilityService::getObjectManager()->create(
                ConfirmationQuestion::class,
                [
                    'question'=> $q,
                    'default' => FALSE
                ]
            );

            $helper = $this->getHelper('question');
            $answer = $helper->ask($input, $output, $question);

            if ($answer == 'y') {
                $output->writeln('<info>Truncating tables</info>...');
                $this->jobService->reset();
            } else {
                $output->writeln('<info>Aborting reset</info>');
            }

            $output->writeln('<info>Done</info>');

            return;
        }

        if ($input->getOption('start')) {
            if ($this->maintenanceMode->isOn()) {
                $output->writeln('<info>Maintenance mode enabled. Can\'t proceed.</info>');

                return;
            }

            if (!$this->config->shouldRunAudit()) {
                $output->writeln('<info>' . $this->getNotRunningMessage() . '</info>');

                return;
            }

            $output->writeln('<info>Starting new SEO audit job</info>...');
            $this->jobService->startJob();
            $output->writeln('<info>Done</info>');

            return;
        }

        if ($input->getOption('run')) {
            if ($this->maintenanceMode->isOn()) {
                $output->writeln('<info>Maintenance mode enabled. Can\'t proceed.</info>');

                return;
            }

            if (!$this->config->shouldRunAudit()) {
                $output->writeln('<info>' . $this->getNotRunningMessage() . '</info>');

                return;
            }

            $timeoutReached = false;
            $start          = microtime(true);
            $ticks          = 1;

            $timeLimit = (int)$input->getOption('limit') ?: 10 * 60;

            if ($timeLimit < 30) { // min 30 seconds
                $timeLimit = 30;
            } elseif ($timeLimit > 3600) { // max 1 hour
                $timeLimit = 3600;
            }

            $runningJob = $this->jobRepository->getRunningJob();

            if (!$runningJob) {
                $output->writeln('No running jobs present. Please start the job first.');
            }

            $now       = new \Zend_Date(microtime(true), \Zend_Date::TIMESTAMP);
            $startedAt = new \Zend_Date(strtotime($runningJob->getStartedAt()), \Zend_Date::TIMESTAMP);

            if ($now->toString('d') !== $startedAt->toString('d')) {
                $output->writeln('<info>Starting new SEO audit job</info>...');
                $runningJob = $this->jobService->startJob();
                $output->writeln('<info>Done</info>');
            }

            $output->writeln('<info>Job is running</info> Time limit: ' . $this->prettifyTimeLimit($timeLimit));

            /** @var ConsoleOutput $output */
            while ($collection = $this->urlRepository->getUnprocessedUrlsCollection()) {
                if (!$collection->count()) {
                    break;
                }

                foreach ($collection as $url) {
                    if (microtime(true) - $start >= $timeLimit) {
                        $timeoutReached = true;

                        break 2;
                    }

                    $section = $output->section();
                    $section->writeln("Processing {$url->getUrl()} ({$url->getType()}) ... ");

                    $this->jobService->retrieveChildUrls($url, $runningJob->getId());
                    $section->overwrite("Processing {$url->getUrl()} ({$url->getType()}) ... <info>Crawled</info> ...");
                    $this->checkService->runChecksForUrl($url);

                    $section->overwrite("Processing {$url->getUrl()} ({$url->getType()}) ... <info>Crawled</info> ... <info>Checked</info>");

                    if (microtime(true) - $start >= 2 * 60 * $ticks) {

                        $section = $output->section();
                        $section->writeln('Aggregating checks results ... ');

                        $result = $this->checkResultService->agregateResult($runningJob);

                        $runningJob->setResult($result);
                        $this->jobRepository->save($runningJob);
                        $ticks++;

                        $section->overwrite('Aggregating checks results ... <info>Done</info>');
                    }
                }
            }

            $section = $output->section();
            $section->writeln('Aggregating checks results ... ');
            $runningJob->setResult($this->checkResultService->agregateResult($runningJob));
            $this->jobRepository->save($runningJob);
            $section->overwrite('Aggregating checks results ... <info>Done</info>');

            $message = $timeoutReached
                ? 'Job finised due to timeout (' . $this->prettifyTimeLimit($timeLimit) . ')'
                : 'All URLs processed';

            $output->writeln($message);

            return;
        }

        if ($input->getOption('stop')) {
            $output->writeln('<info>Finishing running jobs</info>...');
            $this->jobService->finishJob();
            $output->writeln('<info>All Finished</info>');

            return;
        }

        $help = new HelpCommand();
        $help->setCommand($this);
        $help->run($input, $output);
    }

    private function prettifyTimeLimit(int $timeLimit): string
    {
        $minutes = floor($timeLimit / 60);
        $seconds = $timeLimit - $minutes * 60;

        $output = '';

        if ($minutes) {
            $output .= $minutes . ' minute(s) ';
        }

        return $output . $seconds . ' second(s)';
    }

    private function getNotRunningMessage(): string
    {
        $massage = 'Can\'t run SEO Audit. ';

        if (!$this->config->isEnabled()) {
            $massage .= 'SEO Audit disabled in configurations';
        } else {
            $massage .= ' Server load is too high (' . $this->config->getServerLoadRate() . '%)';
        }

        return $massage;
    }
}
