<?php

namespace Oaattia\Downloader\Commands;

use GuzzleHttp\Client;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Oaattia\Downloader\Exceptions\InvalidPageContentExceptions;
use Oaattia\Downloader\LaraCastsFetcher;
use Oaattia\Downloader\Resolver;
use SebastianBergmann\CodeCoverage\Report\Html\File;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LaraCastDownloadCommand extends Command
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('laracasts:download')
            ->addArgument('url', InputArgument::REQUIRED, 'Add the page url')
            ->setDescription('Download the page lessons.')
            ->setHelp("This command allows to get list of the files from the page by passing the page url as argument...");
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     * @throws InvalidPageContentExceptions
     * @throws \Oaattia\Downloader\NoDownloadLinkException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fetcher = new LaraCastsFetcher(new Client(), new Resolver(), new Filesystem(new Local(getenv('DOWNLOAD'))));

        $output->write('<comment>Fetching page links ... </comment>');
        $lessonLinks = $fetcher->getPageLinks($input->getArgument('url'));
        $output->write('<info>done </info>', true);
        
        $pagesContents = [];
        $output->write('<comment>Fetching page content ... </comment>');
        foreach ($lessonLinks as $link) {
            $pagesContents[] = $fetcher->getPageContent($link);
        }
        $output->write('<info>done </info>', true);
        
        $output->write('<comment>Fetching download links from page ... </comment>');
        if (! empty($pagesContents)) {
            $resolver = new Resolver();
            foreach ($pagesContents as $content) {
                $lessonTitle   = $resolver->getLessonsTitle($content);
                $downloadLinks[] = $fetcher->getDownloadLinkFromPage($content);
            }
        } else {
            throw new InvalidPageContentExceptions('Can\'t get page content');
        }
        $output->write('<info>done </info>', true);
        
        $output->writeln('<comment>Fetching download links from page ... </comment>');
        if (isset($downloadLinks)) {
            foreach ($downloadLinks as $key => $link) {
                $fetcher->downloadLink($link, $output, $lessonTitle[$key]);
            }
        }
    
        $output->writeln('<comment>Download finished ... </comment>');
    }
}
