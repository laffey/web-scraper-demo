<?php

namespace EIdeas\OpenPayments\ScraperBundle\Document\Query;

use EIdeas\OpenPayments\ScraperBundle\Document\DocumentManager;


/**
 * Class OpColumnQuery
 * Run various queries and get info on the column definitions
 *
 * @package EIdeas\OpenPayments\ScraperBundle\Document\Query
 */
class OpColumnQuery {

    /**
     * columns on which we will run aggregations, for search hinting
     * @var array
     */
    private $aggregateColumns = array(
        'program_year'              => 'Program Year',
        'payment_publication_date'  => 'Payment Publication Date',
        'submitting_applicable_manufacturer_or_applicable_gpo_name'=> 'Manufacturer',
        'teaching_hospital_name'    => 'Teach Hospital',
        'physician_profile_id'      => 'Physician Profile ID',
        'physician_last_name'       => 'Physician Last Name',
    );

    /**
     * @var DocumentManager
     */
    protected $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * return aggregate columns
     * @return array
     */
    public function getAggregateColumns()
    {
        return $this->aggregateColumns;
    }

    /**
     * return true if the columnName is valid for searching within doctor payments
     *
     * @param $columnName
     * @return bool
     */
    public function isValidSearchColumn($columnName)
    {
        return array_key_exists($columnName, $this->aggregateColumns);
    }

    /**
     * convert the column name into a valid model property
     *
     * @param $columnName
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getModelPropertyName($columnName)
    {
        if (!array_key_exists($columnName, $this->aggregateColumns)) {
            throw new \InvalidArgumentException("Invalid column name given");
        }
        return $this->camelCase($columnName);;
    }

    /**
     * get the aggregate info for a column
     *   returns an array of values and counts per value
     *
     * @param $columnName
     * @return array
     * @throws \InvalidArgumentException
     */
    public function aggregate($columnName)
    {
        if (!array_key_exists($columnName, $this->aggregateColumns)) {
            throw new \InvalidArgumentException("Invalid column name given");
        }
        $qb = $this->documentManager->getRepository('doctor_payment')->createQueryBuilder();
        $field = $this->camelCase($columnName);
        $qb->distinct($field);
        /**
         * @var \Doctrine\MongoDB\ArrayIterator $resultSet
         */
        $resultSet = $qb->getQuery()->execute();
        $aggregates = [];
        foreach ($resultSet as $fieldValue) {
            $qb = $this->documentManager->getRepository('doctor_payment')->createQueryBuilder();
            $qb->field($field)->equals($fieldValue)->count();
            $countResult = $qb->getQuery()->execute();
            $aggregates[] = array('value' => $fieldValue, 'count' => $countResult);
        }
        return $aggregates;
    }

    /**
     * Make an underscored string camel case
     * @param string $string
     * @return string
     */
    private function camelCase($string)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }

}