<?php

namespace EIdeas\OpenPayments\ScraperBundle\Scraper\Adapter;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Exception\RequestException;

/**
 * Class TableDefinitionAdapter
 * grab table definition and column info
 *
 * @package EIdeas\OpenPayments\ScraperBundle\Scraper\Adapter
 */
class TableDefinitionAdapter extends AbstractAdapter
{

    const TYPE = 'table_definition_request';

    /**
     * appended to base url
     * @var string
     */
    protected $path = '/api/views/hrpy-hqv8.json?accessType=WEBSITE';


    /**
     * Use $params to set headers, body, cookies
     *
     * @param array $params
     * @return void
     */
    public function query(GuzzleClient $client, $params)
    {
        try {
            $this->response = $client->get(
                $this->urlBase . $this->path,
                $params
            );
        } catch (RequestException $e) {
            $this->setError('Guzzle ajax request for table definition threw an exception', $e);
            return;
        }

        if ($this->response->getStatusCode() != 200) {
            //db save the status code, and the job as failed, then return
            $this->setError('Table definition request returned a non 200 status code');
        }

    }

    /**
     * Try decoding the body and return a hash array
     *      If this fails, an error will be set
     *
     * @return array
     */
    public function getData()
    {
        $data = [];
        try {
            $data = $this->response->json();
        } catch (ParseException $e) {
            $this->setError('Body not in valid json format.', $e);
        } catch (\RuntimeException $e) {
            $this->setError('Body not in valid json format.', $e);
        }

        //validate important indexes
        if (!$this->hasError() &&
            (empty($data['indexUpdatedAt']) || empty($data['rowsUpdatedAt'])
                || empty($data['columns'])
                || !is_array($data['columns']))
        ) {
            $this->setError('Data missing required parameters, or not formatted properly');
        }

        return $data;
    }
}