<?php

namespace Oaattia\Downloader\Commands;

use GuzzleHttp\Client;
use Oaattia\Downloader\LaraCastsFetcher;
use Oaattia\Downloader\Resolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LaraCastViewCommand extends Command
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('laracasts:view')
            ->addArgument('url', InputArgument::REQUIRED, 'Add the page url')
            ->setDescription('Show the page lesson titles.')
            ->setHelp("This command allows you to create users...");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fetcher = new LaraCastsFetcher(new Client(), new Resolver());
        list($heading, $lessons) = $fetcher->getPageTitleAndLessonsAndUrls($input->getArgument('url'));

        $table = new Table($output);
        $table = $table->setHeaders(['#', $heading]);

        foreach ($lessons as $row => $lesson) {
            $table  = $table->setRow($row, [++$row, $lesson]);
        }

        $table->render();
    }
}
