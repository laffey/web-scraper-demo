<?php

namespace EIdeas\OpenPayments\ScraperBundle\Controller\Console;

use JMS\Serializer\Exception\RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PaymentQueryCommand
 * querying the payment records via cli
 *
 * @package EIdeas\OpenPayments\ScraperBundle\Controller\Console
 */
class PaymentQueryCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('payment:query')
            ->setDescription('Get lists of payments, search for payments, get aggregate column info. No args will return a full list of payment records.')
            ->addOption(
                'page',
                null,
                InputOption::VALUE_REQUIRED,
                'Each page returns 100 results, use this to paginate')
            ->addOption(
                'count',
                null,
                InputOption::VALUE_NONE,
                'Return a count of all payment records in db. Can be used with a filter'
            )
            ->addArgument(
                'filter',
                InputArgument::IS_ARRAY,
                'ex: payment:query physician_last_name Smith'
            )
            ->addOption(
                'transaction_id',
                null,
                InputOption::VALUE_REQUIRED,
                'Search for a payment record matching given transaction id')
            ->addOption(
                'aggregate',
                null,
                InputOption::VALUE_REQUIRED,
                'Get aggregate results on specified column, eg. --aggregate=program_year')
            ->addOption(
                'aggregate-columns',
                null,
                InputOption::VALUE_NONE,
                'List the aggregate columns available')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('aggregate-columns')) {
            $this->outputAggregateColumns($output);
            return;
        }
        if ($input->getOption('aggregate')) {
            $this->outputAggregateResults($input->getOption('aggregate'), $output);
            return;
        }
        if ($input->getOption('page')) {
            $this->getPaymentRecords($output, (int)$input->getOption('page'));
            return;
        }
        if ($input->getOption('count')) {
            $this->outputCount($input, $output);
            return;
        }
        if ($input->getArgument('filter')) {
            $this->outputFilterQuery($input, $output);
            return;
        }
        if ($input->getOption('transaction_id') !== null) {
            $this->outputTransactionSearch((int)$input->getOption('transaction_id'), $output);
            return;
        }

        //else, if no options, return all
        $this->getPaymentRecords($output);

    }

    /**
     * run a query using given filter, output results
     *
     * @param int $transactionId
     * @param OutputInterface $output
     */
    private function outputTransactionSearch($transactionId, OutputInterface $output)
    {
        if ($transactionId <= 0) {
            throw new \RuntimeException('Invalid transaction id. Must be greater than zero');
        }

        /**
         * @var \EIdeas\OpenPayments\ScraperBundle\Document\Query\DoctorPaymentQuery $doctorPaymentQuery
         */
        $doctorPaymentQuery = $this->getContainer()->get('doctor_payment_query');
        $resultSet = $doctorPaymentQuery->getByTransactionId($transactionId);
        $numOfRecords = 0;
        foreach ($resultSet as $paymentRecord) {
            $output->writeln(print_r((array) $paymentRecord, true));
            $numOfRecords++;
        }
        $output->writeln('========================= EOF');
        $output->writeln('');
        $output->writeln('Num of records: ' . $numOfRecords);
        $output->writeln('');
    }

    /**
     * run a query using given filter, output results
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function outputFilterQuery(InputInterface $input, OutputInterface $output)
    {
        $filter = $input->getArgument('filter');
        if (count($filter) != 2) {
            throw new \RuntimeException('Invalid filter.'
                . 'Must include a field name and then a field value, eg. physician_last_name Smith');
        }

        /**
         * @var \EIdeas\OpenPayments\ScraperBundle\Document\Query\OpColumnQuery $opColumnQuery
         */
        $opColumnQuery = $this->getContainer()->get('op_column_query');
        try {
            $propertyName = $opColumnQuery->getModelPropertyName($filter[0]);
            $dataType = $opColumnQuery->getColumnDataType($filter[0]);
        } catch (\InvalidArgumentException $e) {
            $output->writeln($e->getMessage());
            return;
        }
        $propertyValue = $filter[1];

        /**
         * @var \EIdeas\OpenPayments\ScraperBundle\Document\Query\DoctorPaymentQuery $doctorPaymentQuery
         */
        $doctorPaymentQuery = $this->getContainer()->get('doctor_payment_query');
        if ($dataType == 'number') {
            $propertyValue = (int)$propertyValue;
        }
        $resultSet = $doctorPaymentQuery->filter($propertyName, $propertyValue);
        $numOfRecords = 0;
        foreach ($resultSet as $paymentRecord) {
            $output->writeln(print_r((array) $paymentRecord, true));
            $numOfRecords++;
        }
        $output->writeln('========================= EOF');
        $output->writeln('');
        $output->writeln('Num of records: ' . $numOfRecords);
        $output->writeln('');
    }

    /**
     * Run a count() on payment records, output the result
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function outputCount(InputInterface $input, OutputInterface $output)
    {
        $propertyName = null;
        $propertyValue = null;
        if (!empty($input->getArgument('filter'))) {
            $filter = $input->getArgument('filter');
            if (count($filter) != 2) {
                throw new \RuntimeException('Invalid filter.'
                    . 'Must include a field name and then a field value, eg. physician_last_name Smith');
            }

            /**
             * @var \EIdeas\OpenPayments\ScraperBundle\Document\Query\OpColumnQuery $opColumnQuery
             */
            $opColumnQuery = $this->getContainer()->get('op_column_query');
            try {
                $propertyName = $opColumnQuery->getModelPropertyName($filter[0]);
            } catch (\InvalidArgumentException $e) {
                $output->writeln($e->getMessage());
                return;
            }
            $propertyValue = $filter[1];
        }

        /**
         * @var \EIdeas\OpenPayments\ScraperBundle\Document\Query\DoctorPaymentQuery $doctorPaymentQuery
         */
        $doctorPaymentQuery = $this->getContainer()->get('doctor_payment_query');
        $result = $doctorPaymentQuery->count($propertyName, $propertyValue);
        $output->writeln('Num of records: ' . $result);
        $output->writeln('');
    }

    /**
     * output unfiltered list of payment records
     *
     * @param OutputInterface $output
     * @param int $page
     */
    private function getPaymentRecords(OutputInterface $output, $page = 0)
    {
        /**
         * @var \EIdeas\OpenPayments\ScraperBundle\Document\Query\DoctorPaymentQuery $doctorPaymentQuery
         */
        $doctorPaymentQuery = $this->getContainer()->get('doctor_payment_query');
        $results = $doctorPaymentQuery->all($page);
        $output->writeln(print_r($results, true));
        $output->writeln('======================== eof');
        $output->writeln('');
        $output->writeln('Num of results: ' . count($results));
        $output->writeln('');
    }

    /**
     * output a list of aggregate-able columns
     *
     * @param OutputInterface $output
     */
    private function outputAggregateColumns(OutputInterface $output)
    {
        $opColumnQuery = $this->getContainer()->get('op_column_query');
        $columns = $opColumnQuery->getAggregateColumns();
        $output->writeln('Available aggregate column names:');
        foreach ($columns as $columnName => $columnLabel) {
            $output->writeln($columnName . ' : ' . $columnLabel);
        }
        $output->writeln('');
    }

    /**
     * run an aggregation on given column name, dump results
     *
     * @param string $columnName
     * @param OutputInterface $output
     */
    private function outputAggregateResults($columnName, OutputInterface $output)
    {
        try {
            $resultSet = $this->getContainer()->get('op_column_query')->aggregate($columnName);
            $output->writeln(print_r($resultSet, true));
        } catch (\InvalidArgumentException $e) {
            $output->writeln($e->getMessage());
        }
        $output->writeln('');
    }

}