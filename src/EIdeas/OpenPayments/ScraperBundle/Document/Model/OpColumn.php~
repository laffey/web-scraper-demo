<?php

namespace EIdeas\OpenPayments\ScraperBundle\Document\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class OpColumns
 * Defines the columns returned in Open Payments table definition
 *
 * @package EIdeas\OpenPayments\ScraperBundle\Document\Model
 * @MongoDB\Document
 */
class OpColumn implements ModelInterface {

    /**
     * @var string
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @var int
     * @MongoDB\Int
     * @MongoDB\Index
     */
    protected $opId;

    /**
     * @var string
     * @MongoDB\String
     */
    protected $name;

    /**
     * @var string
     * @MongoDB\String
     */
    protected $dataTypeName;

    /**
     * @var string
     * @MongoDB\String
     * @MongoDB\Index
     */
    protected $fieldName;


    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }




    /**
     * Set opId
     *
     * @param int $opId
     * @return self
     */
    public function setOpId($opId)
    {
        $this->opId = $opId;
        return $this;
    }

    /**
     * Get opId
     *
     * @return int $opId
     */
    public function getOpId()
    {
        return $this->opId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set dataTypeName
     *
     * @param string $dataTypeName
     * @return self
     */
    public function setDataTypeName($dataTypeName)
    {
        $this->dataTypeName = $dataTypeName;
        return $this;
    }

    /**
     * Get dataTypeName
     *
     * @return string $dataTypeName
     */
    public function getDataTypeName()
    {
        return $this->dataTypeName;
    }

    /**
     * Set fieldName
     *
     * @param string $fieldName
     * @return self
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
        return $this;
    }

    /**
     * Get fieldName
     *
     * @return string $fieldName
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }
}
