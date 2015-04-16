<?php

namespace EIdeas\OpenPayments\ScraperBundle\Document\Query;

use EIdeas\OpenPayments\ScraperBundle\Document\DocumentManager;

/**
 * Class DoctorPaymentQuery
 * Runs various queries on the payment db
 *
 * @package EIdeas\OpenPayments\ScraperBundle\Document\Query
 */
class DoctorPaymentQuery {

    /**
     * if the user wants to page records
     */
    const RECORDS_PER_PAGE = 100;

    /**
     * @var DocumentManager
     */
    protected $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * page is optional; default is 0, which means all records will be returned
     *      returns an array of DoctorPayment models
     *
     * @param int $page
     * @return array
     */
    public function all($page = 0)
    {
        $repository = $this->documentManager->getRepository('doctor_payment');
        $limit = null;
        $offset = null;
        if ($page > 0) {
            $limit = self::RECORDS_PER_PAGE;
            $offset = $page * self::RECORDS_PER_PAGE;
        }
        $resultSet = $repository->findBy(
            [],
            ['paymentPublicationDate' => -1, 'programYear' => -1, 'generalTransactionId' => 1],
            $limit,
            $offset
        );
        return $resultSet;
    }

    /**
     * field name and value are optional; if left out, count is total count of
     *    all payment records in db
     *
     * @param string $fieldName
     * @param mixed $fieldValue
     * @return int
     */
    public function count($fieldName = null, $fieldValue = null)
    {
        $qb = $this->documentManager->getRepository('doctor_payment')->createQueryBuilder();
        if (!empty($fieldName) && !empty($fieldValue)) {
            $qb->field($fieldName)->equals(new \MongoRegex('/^' . $fieldValue . '/'));
        }
        return $qb->count()->getQuery()->execute();
    }

    /**
     * filter result set by given field and value
     *
     * @param string $fieldName
     * @param mixed $fieldValue
     * @return \Doctrine\ODM\MongoDB\Cursor
     */
    public function filter($fieldName, $fieldValue)
    {
        $qb = $this->documentManager->getRepository('doctor_payment')->createQueryBuilder();
        if (is_numeric($fieldValue)) {
            $qb->field($fieldName)->equals($fieldValue);
        } else {
            $qb->field($fieldName)->equals(new \MongoRegex('/^' . $fieldValue . '/'));
        }
        return $qb->getQuery()->execute();
    }

    /**
     * look up a record by transaction id
     *
     * @param int $transactionId
     * @return array
     */
    public function getByTransactionId($transactionId)
    {
        return $this->filter('generalTransactionId', $transactionId);
    }

}