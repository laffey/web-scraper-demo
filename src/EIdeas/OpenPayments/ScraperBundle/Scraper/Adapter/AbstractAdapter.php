<?php

namespace EIdeas\OpenPayments\ScraperBundle\Scraper\Adapter;

use GuzzleHttp\Message\ResponseInterface;


abstract class AbstractAdapter implements AdapterInterface {

    const TYPE = 'undefined';

    /**
     * if an error occurs, set the message here
     * @var string
     */
    protected $errorMessage = '';

    /**
     * @var string
     */
    protected $urlBase = '';

    /**
     * appended to base url
     * @var string
     */
    protected $path = '';

    /**
     * set when request is sent
     * @var ResponseInterface
     */
    protected $response;


    public function __construct($urlBase)
    {
        $this->urlBase = $urlBase;
    }

    /**
     * @return string
     */
    public function getFullUrl()
    {
        return $this->urlBase . $this->path;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        if (!empty($this->response)) {
            return (int)$this->response->getStatusCode();
        }
        return 0;
    }

    /**
     * @return string
     */
    public function getResponseHeader()
    {
        if (!empty($this->response)) {
            return json_encode($this->response->getHeaders());
        }
        return '';
    }

    /**
     * @return string
     */
    public function getResponseBody()
    {
        if (!empty($this->response)) {
            $body = $this->response->getBody();
            if (!empty($body)) {
                if ($body->isReadable()) {
                    return $body->getContents();
                }
            }
        }
        return '';
    }

    /**
     * check if an error occurred during query()
     * @return mixed
     */
    public function hasError()
    {
        return !empty($this->errorMessage);
    }

    /**
     * return the error message
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * an error occurred, flag error and set message
     * @param $message
     */
    protected function setError($message, \Exception $e = null)
    {
        $this->errorMessage = $message;
        if (!empty($e)) {
            $this->errorMessage .= "\n======== Exception: " . $e->getMessage();
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return static::TYPE;
    }

}