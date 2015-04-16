<?php

namespace EIdeas\OpenPayments\ScraperBundle\Controller\Console;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Called from the cli, app/console scraper:run
 * Will run the site scraper and save new data
 * 
 *
 */
class ScraperCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('scraper:run')
            ->setDescription('Run the OpenPaymentsData website scraper. New data will be saved.')
            ->addOption(
                'offset',
                null,
                InputOption::VALUE_REQUIRED,
                'Skip n records. Optional and defaults to 0')
            ->addOption(
                'limit',
                null,
                InputOption::VALUE_REQUIRED,
                'Only search through n many records. Optional, defaults to 50.')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $offset = (int)$input->getOption('offset');
        $limit = (int)$input->getOption('limit');

        /**
         * @var \EIdeas\OpenPayments\ScraperBundle\Scraper\SiteScraper $scraper
         */
        $scraper = $this->getContainer()->get('site_scraper');
        $scraper->setOutputHandler($output);
        if ($scraper->run($limit, $offset)) {
            $this->finalMessage($output, 'Done!');
        } else {
            $this->finalMessage($output, 'Job failed..', false);
        }
    }
    
    /**
     * output a styled message
     * 
     * @param OutputInterface $output
     * @param string $message
     * @param boolean $success      *default = true
     */
    private function finalMessage(OutputInterface $output, $message, $success=true)
    {
        $output->writeln('');
        if ($success) {
            $output->writeln('<bg=green;options=bold>' . $message . '</bg=green;options=bold>');
        } else {
            $output->writeln('<bg=red;options=bold>' . $message . '</bg=red;options=bold>');
        }
        $output->writeln('');
    }
}