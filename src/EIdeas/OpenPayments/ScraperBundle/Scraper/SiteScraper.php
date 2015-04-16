<?php

namespace EIdeas\OpenPayments\ScraperBundle\Scraper;

use EIdeas\OpenPayments\ScraperBundle\Scraper\Adapter\AdapterFactory;
use EIdeas\OpenPayments\ScraperBundle\Scraper\Adapter\QueryAdapter;
use EIdeas\OpenPayments\ScraperBundle\Scraper\Adapter\TableDefinitionAdapter;
use EIdeas\OpenPayments\ScraperBundle\Scraper\Job\PaymentProcessorException;
use EIdeas\OpenPayments\ScraperBundle\Scraper\Job\ScraperJob;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This is the main class which parses the website for payment data,
 * saving the data into mongo
 *
 */
class SiteScraper
{
    /**
     * 
     * @var GuzzleClient
     */
    protected $client;
    
    /**
     * subscriber to store cookie info
     * @var CookieJar
     */
    protected $cookiePlugin;
    
    /**
     * request headers
     * 
     * @var array
     */
    protected $headers = array(
        'Accept' => 'application/json, text/javascript, */*; q=0.01',
        'Accept-Language' => 'en-US,en;q=0.8,es;q=0.6',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36',
        'Content-Type' => 'application/json',
        'X-Requested-With' => 'XMLHttpRequest',
    );
    
    /**
     * run() sets this, so we don't have to inject the output interface
     *   into each function
     *   
     * @var OutputInterface
     */
    protected $outputHandler;

    /**
     * @var AdapterFactory
     */
    protected $adapterFactory;

    /**
     * @var ScraperJob
     */
    protected $scraperJob;

    /**
     * 
     * @param GuzzleClient $client
     * @param CookieJar $cookiePlugin
     * @param AdapterFactory $adapterFactory
     * @param ScraperJob $scraperJob
     */
    public function __construct(
            GuzzleClient $client,
            CookieJar $cookiePlugin,
            AdapterFactory $adapterFactory,
            ScraperJob $scraperJob)
    {
        $this->client = $client;
        $this->cookiePlugin = $cookiePlugin;
        $this->adapterFactory = $adapterFactory;
        $this->scraperJob = $scraperJob;
    }
    
    /**
     * try to run the site scraper, saving new data since last run
     * 
     * returns true upon success, false if something went wrong
     * 
     * @param int $limit
     * @param int $offset
     * @return bool
     * @throws \RuntimeException            *when no output handler set
     */
    public function run($limit = 0, $offset = 0)
    {
        if (empty($this->outputHandler)) {
            throw new \RuntimeException('Output handler must be set before calling run()');
        }
        if ($offset < 0) {
            throw new \RuntimeException('Invalid arg: offset must be a value of zero or greater');
        }

        $this->scraperJob->startNew($offset, $limit);
        /**
         * note: for this project, I am limiting $limit by default to 50
         *   in a real-world I'd probably have an initialRun() job, that loops and gathers all records
         *   with proceeding runs then looping new records, stopping when an existing record is found
         */
        if ($limit <= 0) {
            $limit = 50;
        }

        // set cookies persistent during life of client
        $requestParams = ['cookies' => true, 'headers' => &$this->headers];

        $this->scraperJob->update(TableDefinitionAdapter::TYPE);
        if (!$this->runGetTableDefinition($requestParams)) {
            return false;
        }
        
        $this->scraperJob->update(QueryAdapter::TYPE);
        
        //now run the payment queriesdb
        //do 50 records at a time
        if ($limit > 50) {
            $max = floor($limit / 50);
            $remaining = $limit % 50;
            for ($i = 0; $i < $max; $i++) {
                if (!$this->runQueryRequest($requestParams, $offset, 50)) {
                    return false;
                }
                $offset += 50;
            }
            if ($remaining > 0) {
                if (!$this->runQueryRequest($requestParams, $offset, $remaining)) {
                    return false;
                }
            }
        } else {
            // a single call is sufficient
            if (!$this->runQueryRequest($requestParams, $offset, $limit)) {
                return false;
            }
        }

        $this->scraperJob->complete(true);
        return true;
    }
    
    /**
     * output failed message and return false
     *  optionally add an exception object, for which the output will append the exception message
     *  
     * @param string $message
     * @param \Exception $e
     * @return bool       *always false
     */
    protected function failJob($message, \Exception $e = null)
    {
        if (!empty($e)) {
            $message .= "\n" . 'Exception: ' . $e->getMessage();
        }
        $this->outputHandler->writeln('Job Failed -------------------');
        $this->outputHandler->writeln($message);

        //update job status
        $this->scraperJob->complete(false);
        return false;
    }
    
    /**
     * print the status code from http response
     *  *type = identifier label for the request made
     *  
     * @param string $type
     * @param int $statusCode
     * @return void
     */
    protected function printStatusCode($type, $statusCode)
    {
        $this->outputHandler->writeln('Status Code (' . $type . '):' . $statusCode);
    }
    
    /**
     * Set the handler for printout output
     * 
     * @param OutputInterface $output
     * @return void
     */
    public function setOutputHandler(OutputInterface $output)
    {
        $this->outputHandler = $output;
    }

    /**
     * @param array $requestParams
     * @return bool
     */
    private function runGetTableDefinition(Array &$requestParams)
    {
        $type = TableDefinitionAdapter::TYPE;

        /**
         * @var \EIdeas\OpenPayments\ScraperBundle\Scraper\Adapter\TableDefinitionAdapter $adapter
         */
        $adapter = $this->adapterFactory->get($type);
        $adapter->query($this->client, $requestParams);
        if ($adapter->hasError()) {
            return $this->failJob($adapter->getErrorMessage());
        }
        $viewContent = $adapter->getResponseBody();
        $this->scraperJob->saveRawResponse(
            $type,
            $adapter->getFullUrl(),
            $adapter->getStatusCode(),
            json_encode($requestParams['headers']),
            $adapter->getResponseHeader(),
            $viewContent
        );

        $this->printStatusCode($type, $adapter->getStatusCode());

        $data = $adapter->getData();
        if ($adapter->hasError()) {
            return $this->failJob($adapter->getErrorMessage());
        }

        $this->scraperJob->saveColumnData(
            $data['columns'],
            (int)$data['indexUpdatedAt'],
            (int)$data['rowsUpdatedAt']
        );

        //open data likes us to send a view to the query requests
        if (empty($viewContent)) {
            return $this->failJob('Response body for the table definition was empty.. cannot continue');
        }
        $e = strrpos($viewContent, '}');
        $requestParams['body'] = substr($viewContent, 0, $e)
            . ',"query":{"orderBys":[{"expression":{"columnId":180496833,"type":"column"},"ascending":false},{"expression":{"columnId":180496831,"type":"column"},"ascending":false}]}'
            . ',"originalViewId":"hrpy-hqv8","displayFormat":{}}';
        return true;
    }

    /**
     * @param array $requestParams
     * @param int $offset
     * @param int $limit
     * @return bool
     */
    private function runQueryRequest(Array &$requestParams, $offset, $limit)
    {
        $type = QueryAdapter::TYPE;

        /**
         * @var \EIdeas\OpenPayments\ScraperBundle\Scraper\Adapter\QueryAdapter $adapter
         */
        $adapter = $this->adapterFactory->get($type);
        $adapter->setOffsetAndLimit($offset, $limit)->query($this->client, $requestParams);
        if ($adapter->hasError()) {
            return $this->failJob($adapter->getErrorMessage());
        }
        $this->scraperJob->saveRawResponse(
            $type,
            $adapter->getFullUrl(),
            $adapter->getStatusCode(),
            json_encode($requestParams['headers']),
            $adapter->getResponseHeader(),
            $adapter->getResponseBody()
        );

        $this->printStatusCode($type, $adapter->getStatusCode());

        $data = $adapter->getData();
        if ($adapter->hasError()) {
            return $this->failJob($adapter->getErrorMessage());
        }

        //save records
        $doctorPaymentProcessor = $this->scraperJob->getDoctorPaymentProcessor();
        $jobId = $this->scraperJob->getJobId();
        foreach ($data as $paymentRecord) {
            try {
                $doctorPaymentProcessor->save($paymentRecord, $jobId);
            } catch (PaymentProcessorException $e) {
                //record will be skipped, but loop will continue
                $this->outputHandler->writeln('Record skipped! Processing error:' . $e->getMessage());
            } catch (\RuntimeException $e) {
                // entire job fails
                return $this->failJob('Payment processor failed on save().', $e);
            }
        }
        return true;
    }
}