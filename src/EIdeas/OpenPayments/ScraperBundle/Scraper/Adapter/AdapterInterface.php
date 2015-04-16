<?php

namespace EIdeas\OpenPayments\ScraperBundle\Scraper\Adapter;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Interface AdapterInterface
 *   Define the interface for the OP web scraping adapters
 *   each adapter knows what response to expect and how to parse it
 *
 * @package EIdeas\OpenPayments\ScraperBundle\Scraper\Adapter
 */
interface AdapterInterface {

    /**
     * Use $params to set headers, body, cookies
     *
     * @param array $params
     * @return void
     */
    public function query(GuzzleClient $client, $params);

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getFullUrl();

    /**
     * @return int
     */
    public function getStatusCode();

    /**
     * @return string
     */
    public function getResponseHeader();

    /**
     * @return string
     */
    public function getResponseBody();

    /**
     * check if an error occurred during query()
     * @return mixed
     */
    public function hasError();

    /**
     * return the error message
     * @return string
     */
    public function getErrorMessage();

}