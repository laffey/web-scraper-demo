<?php

namespace EIdeas\OpenPayments\ScraperBundle\Document\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class RawResponse
 * saves the raw responses for later evaluation as needed
 *
 * @package EIdeas\OpenPayments\ScraperBundle\Document
 * @MongoDB\Document
 */
class RawResponse implements ModelInterface
{
    /**
     * @var string
     * @MongoDB\Id
     */
    protected $id;

    /**
     * related ScraperJob
     * @var ScraperJob
     * @MongoDB\ReferenceOne(targetDocument="ScraperJob")
     * @MongoDB\Index
     */
    protected $job;

    /**
     * @var \DateTime
     * @MongoDB\Date
     * @MongoDB\Index
     */
    protected $createdDate;

    /**
     * request url + path + query
     * @var string
     * @MongoDB\String
     */
    protected $url;

    /**
     * dump of headers
     * @var string
     * @MongoDB\String
     */
    protected $requestHeader;

    /**
     * which adapter made the call?
     * @var string
     * @MongoDB\String
     */
    protected $adapterType;

    /**
     * status code of response
     * @var int
     * @MongoDB\Int
     */
    protected $statusCode;

    /**
     * dump of response header
     * @var string
     * @MongoDB\String
     */
    protected $responseHeader;

    /**
     * dump of response body
     * @var string
     * @MongoDB\String
     */
    protected $responseBody;

    /**
     * @return string
     * @MongoDB\String
     */
    public function getId()
    {
        return $this->id;
    }




    /**
     * Set job
     *
     * @param \EIdeas\OpenPayments\ScraperBundle\Document\Model\ScraperJob $job
     * @return self
     */
    public function setJob(\EIdeas\OpenPayments\ScraperBundle\Document\Model\ScraperJob $job)
    {
        $this->job = $job;
        return $this;
    }

    /**
     * Get job
     *
     * @return \EIdeas\OpenPayments\ScraperBundle\Document\Model\ScraperJob $job
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get url
     *
     * @return string $url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set adapterType
     *
     * @param string $adapterType
     * @return self
     */
    public function setAdapterType($adapterType)
    {
        $this->adapterType = $adapterType;
        return $this;
    }

    /**
     * Get adapterType
     *
     * @return string $adapterType
     */
    public function getAdapterType()
    {
        return $this->adapterType;
    }

    /**
     * Set statusCode
     *
     * @param int $statusCode
     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Get statusCode
     *
     * @return int $statusCode
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set responseBody
     *
     * @param string $responseBody
     * @return self
     */
    public function setResponseBody($responseBody)
    {
        $this->responseBody = $responseBody;
        return $this;
    }

    /**
     * Get responseBody
     *
     * @return string $responseBody
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return self
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime $createdDate
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }


    /**
     * Set requestHeader
     *
     * @param string $requestHeader
     * @return self
     */
    public function setRequestHeader($requestHeader)
    {
        $this->requestHeader = $requestHeader;
        return $this;
    }

    /**
     * Get requestHeader
     *
     * @return string $requestHeader
     */
    public function getRequestHeader()
    {
        return $this->requestHeader;
    }

    /**
     * Set responseHeader
     *
     * @param string $responseHeader
     * @return self
     */
    public function setResponseHeader($responseHeader)
    {
        $this->responseHeader = $responseHeader;
        return $this;
    }

    /**
     * Get responseHeader
     *
     * @return string $responseHeader
     */
    public function getResponseHeader()
    {
        return $this->responseHeader;
    }
}
