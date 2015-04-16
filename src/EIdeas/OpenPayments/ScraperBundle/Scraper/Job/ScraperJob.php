<?php

namespace EIdeas\OpenPayments\ScraperBundle\Scraper\Job;

use EIdeas\OpenPayments\ScraperBundle\Document\DocumentManager;
use EIdeas\OpenPayments\ScraperBundle\Document\Model\ScraperJob as ScraperJobModel;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Class ScraperJob
 * A scraper job tracks the jobs progress, and saves the info and related responses in the db
 *
 * @package EIdeas\OpenPayments\ScraperBundle\Scraper\Job
 */
class ScraperJob {

    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETE = 'complete';
    const STATUS_FAILED = 'failed';

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * persist through lifetime of job run
     * @var ScraperJobModel
     */
    private $scraperJobModel;

    /**
     * @var DoctorPaymentProcessor
     */
    private $doctorPaymentProcessor;

    public function __construct(DocumentManager $documentManager, DoctorPaymentProcessor $doctorPaymentProcessor)
    {
        $this->documentManager = $documentManager;
        $this->doctorPaymentProcessor = $doctorPaymentProcessor;
    }

    /**
     * start a new job
     * @param int $offset
     * @param int $limit
     */
    public function startNew($offset, $limit)
    {
        $this->scraperJobModel = $this->documentManager->getModel('scraper_job');
        $this->scraperJobModel->setCreateDate(date('Y-m-d H:i:s'))
                              ->setStatus(self::STATUS_IN_PROGRESS)
                              ->setCliOffset($offset)
                              ->setCliLimit($limit);
        $this->documentManager->save($this->scraperJobModel);
    }

    /**
     * @param string $step
     */
    public function update($step)
    {
        $this->scraperJobModel->setStep($step)
                              ->setUpdateDate(date('Y-m-d H:i:s'));
        $this->documentManager->save($this->scraperJobModel);
    }

    /**
     * save column data retrieved by the OpenPayments db
     *
     * @param array $columnData
     * @param int $dbIndexUpdatedAt
     * @param int $dbRowsUpdatedAt
     */
    public function saveColumnData(Array $columnData, $dbIndexUpdatedAt, $dbRowsUpdatedAt)
    {
        $this->scraperJobModel->setOpDbIndexUpdatedAt($dbIndexUpdatedAt)
                              ->setOpDbRowsUpdatedAt($dbRowsUpdatedAt);
        $this->documentManager->delaySave($this->scraperJobModel);

        //now look at column data
        $repository = $this->documentManager->getRepository('op_column');
        foreach ($columnData as $column) {
            if (!empty($column['fieldName'])) {
                $this->saveColumnDefinition($column, $repository);
            }
        }
    }

    /**
     * finish a job and save its final status
     *      $status = true means job was successful
     *          otherwise it failed
     *
     * @param bool $status
     */
    public function complete($status)
    {
        if ($status) {
            $this->scraperJobModel->setStatus(self::STATUS_COMPLETE);
        } else {
            $this->scraperJobModel->setStatus(self::STATUS_FAILED);
        }
        $this->scraperJobModel->setUpdateDate(date('Y-m-d H:i:s'))
                              ->setNewRecordsProcessed($this->doctorPaymentProcessor->getRecordCount());
        $this->documentManager->save($this->scraperJobModel);
    }

    /**
     * Save the raw response related to the job
     *
     * @param string $type
     * @param string $url
     * @param int $statusCode
     * @param string $requestHeader
     * @param string $responseHeader
     * @param string $body
     */
    public function saveRawResponse($type, $url, $statusCode, $requestHeader, $responseHeader, $body)
    {
        /**
         * @var \EIdeas\OpenPayments\ScraperBundle\Document\Model\RawResponse $rawResponseModel
         */
        $rawResponseModel = $this->documentManager->getModel('raw_response');
        $rawResponseModel->setCreatedDate(date('Y-m-d H:i:s'))
                         ->setUrl($url)
                         ->setAdapterType($type)
                         ->setStatusCode($statusCode)
                         ->setRequestHeader($requestHeader)
                         ->setResponseHeader($responseHeader)
                         ->setResponseBody($body)
                         ->setJob($this->scraperJobModel);
        $this->documentManager->save($rawResponseModel);
    }

    /**
     * check if column already exists in db
     *  if not, add
     *
     * @param array $columnData
     * @param DocumentRepository $repository
     */
    private function saveColumnDefinition(Array $columnData, DocumentRepository $repository)
    {
        $opColumn = $repository->findOneBy(['opId' => $columnData['id']]);
        if (empty($opColumn)) {
            /**
             * @var \EIdeas\OpenPayments\ScraperBundle\Document\Model\OpColumn $opColumn
             */
            $opColumn = $this->documentManager->getModel('op_column');
            $opColumn->setOpId($columnData['id'])
                     ->setName($columnData['name'])
                     ->setDataTypeName($columnData['dataTypeName'])
                     ->setFieldName($columnData['fieldName']);
            $this->documentManager->save($opColumn);
        }
    }

    /**
     * @return DoctorPaymentProcessor
     */
    public function getDoctorPaymentProcessor()
    {
        return $this->doctorPaymentProcessor;
    }

    /**
     * return the document's job id
     * @return string || null           *if job model not created, returns null
     */
    public function getJobId()
    {
        if ($this->scraperJobModel) {
            return $this->scraperJobModel->getId();
        }
        return null;
    }

}