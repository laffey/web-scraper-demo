<?php

namespace EIdeas\OpenPayments\ScraperBundle\Scraper\Adapter;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Exception\RequestException;

/**
 * Class QueryAdapter
 * Runs the queries, grabbing payment records from open payments
 *
 * @package EIdeas\OpenPayments\ScraperBundle\Scraper\Adapter
 */
class QueryAdapter extends AbstractAdapter {

    const TYPE = 'ajax_query_request';

    /**
     * appended to base url
     * @var string
     */
    protected $path = '/views/INLINE/rows.json?accessType=WEBSITE&method=getByIds&asHashes=true&start=%d&length=%d';

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var int
     */
    protected $limit = 0;


    /**
     * Use $params to set headers, body, cookies
     *
     * @param array $params
     * @return void
     */
    public function query(GuzzleClient $client, $params)
    {
        try {
            $this->response = $client->post(
                $this->getFullUrl(),
                $params
            );
        } catch (RequestException $e) {
            $this->setError('Query request threw an exception', $e);
            return;
        }

        if ($this->response->getStatusCode() != 200) {
            //db save the status code, and the job as failed, then return
            $this->setError('Query request returned a non 200 status code');
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

        return $data;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return QueryAdapter
     */
    public function setOffsetAndLimit($offset, $limit)
    {
        $this->offset = $offset;
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullUrl()
    {
        return $this->urlBase . sprintf($this->path, $this->offset, $this->limit);
    }

}