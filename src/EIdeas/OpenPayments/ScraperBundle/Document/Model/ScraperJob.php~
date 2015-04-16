<?php

namespace EIdeas\OpenPayments\ScraperBundle\Document\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Class ScraperJob
 * model with info on a job run
 *
 * @package EIdeas\OpenPayments\ScraperBundle\Document
 * @MongoDB\Document
 */
class ScraperJob implements ModelInterface
{

    /**
     * @var string
     * @MongoDB\Id
     */
    protected $id;

    /**
     * job initated at
     * @var \DateTime
     * @MongoDB\Date
     */
    protected $createDate;

    /**
     * last update time
     * @var \DateTime
     * @MongoDB\Date
     */
    protected $updateDate;

    /**
     * in progress, complete, failed
     * @var string
     * @MongoDB\String
     */
    protected $status;

    /**
     * if job failed, what step did it get to?
     * @var string
     * @MongoDB\String
     */
    protected $step;

    /**
     * cli arg for limit
     * @var int
     * @MongoDB\Int
     */
    protected $cliLimit;

    /**
     * cli arg for offset
     * @var int
     * @MongoDB\Int
     */
    protected $cliOffset;

    /**
     * how many new records were discovered and saved?
     * @var int
     * @MongoDB\Int
     */
    protected $newRecordsProcessed = 0;

    /**
     * value from table definition call
     * @var int
     * @MongoDB\Int
     */
    protected $opDbIndexUpdatedAt;

    /**
     * value from table definition call
     * @var int
     * @MongoDB\Int
     */
    protected $opDbRowsUpdatedAt;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set createDate
     *
     * @param date $createDate
     * @return self
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
        return $this;
    }

    /**
     * Get createDate
     *
     * @return date $createDate
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set updateDate
     *
     * @param date $updateDate
     * @return self
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;
        return $this;
    }

    /**
     * Get updateDate
     *
     * @return date $updateDate
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set status
     *
     * @param int $status
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return int $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set step
     *
     * @param string $step
     * @return self
     */
    public function setStep($step)
    {
        $this->step = $step;
        return $this;
    }

    /**
     * Get step
     *
     * @return string $step
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Set cliLimit
     *
     * @param int $cliLimit
     * @return self
     */
    public function setCliLimit($cliLimit)
    {
        $this->cliLimit = $cliLimit;
        return $this;
    }

    /**
     * Get cliLimit
     *
     * @return int $cliLimit
     */
    public function getCliLimit()
    {
        return $this->cliLimit;
    }

    /**
     * Set cliOffset
     *
     * @param int $cliOffset
     * @return self
     */
    public function setCliOffset($cliOffset)
    {
        $this->cliOffset = $cliOffset;
        return $this;
    }

    /**
     * Get cliOffset
     *
     * @return int $cliOffset
     */
    public function getCliOffset()
    {
        return $this->cliOffset;
    }

    /**
     * Set newRecordsProcessed
     *
     * @param int $newRecordsProcessed
     * @return self
     */
    public function setNewRecordsProcessed($newRecordsProcessed)
    {
        $this->newRecordsProcessed = $newRecordsProcessed;
        return $this;
    }

    /**
     * Get newRecordsProcessed
     *
     * @return int $newRecordsProcessed
     */
    public function getNewRecordsProcessed()
    {
        return $this->newRecordsProcessed;
    }

    /**
     * Set opDbIndexUpdatedAt
     *
     * @param int $opDbIndexUpdatedAt
     * @return self
     */
    public function setOpDbIndexUpdatedAt($opDbIndexUpdatedAt)
    {
        $this->opDbIndexUpdatedAt = $opDbIndexUpdatedAt;
        return $this;
    }

    /**
     * Get opDbIndexUpdatedAt
     *
     * @return int $opDbIndexUpdatedAt
     */
    public function getOpDbIndexUpdatedAt()
    {
        return $this->opDbIndexUpdatedAt;
    }

    /**
     * Set opDbRowsUpdatedAt
     *
     * @param int $opDbRowsUpdatedAt
     * @return self
     */
    public function setOpDbRowsUpdatedAt($opDbRowsUpdatedAt)
    {
        $this->opDbRowsUpdatedAt = $opDbRowsUpdatedAt;
        return $this;
    }

    /**
     * Get opDbRowsUpdatedAt
     *
     * @return int $opDbRowsUpdatedAt
     */
    public function getOpDbRowsUpdatedAt()
    {
        return $this->opDbRowsUpdatedAt;
    }
}
