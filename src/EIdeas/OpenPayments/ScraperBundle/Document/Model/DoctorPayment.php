<?php

namespace EIdeas\OpenPayments\ScraperBundle\Document\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\DBAL\Types\Type;

/**
 * Class DoctorPayment
 * Note - I defined the fields which I wanted indexable / searchable
 *   all other fields can be lumped into miscColumns
 *
 * @package EIdeas\OpenPayments\ScraperBundle\Document\Model
 * @MongoDB\Document
 */
class DoctorPayment implements ModelInterface {

    /**
     * @var string
     * @MongoDB\Id
     */
    protected $id;

    /**
     * the related job id
     * @var int
     * @MongoDB\Int
     * @MongoDB\Index
     */
    protected $scraperJobId;

    /**
     * Date we saved the record
     * @var \DateTime
     * @MongoDB\Date
     */
    protected $savedOn;

    /**
     * @var \DateTime
     * @MongoDB\Date
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @MongoDB\Date
     */
    protected $updatedAt;

    /**
     * @var int
     * @MongoDB\Int
     * @MongoDB\Index(unique=true)
     */
    protected $generalTransactionId;

    /**
     * @var int
     * @MongoDB\Int
     * @MongoDB\Index
     */
    protected $programYear;

    /**
     * @var \DateTime
     * @MongoDB\Date
     * @MongoDB\Index
     */
    protected $paymentPublicationDate;

    /**
     * @var string
     * @MongoDB\String
     * @MongoDB\Index
     */
    protected $submittingApplicableManufacturerOrApplicableGpoName;

    /**
     * @var string
     * @MongoDB\String
     * @MongoDB\Index
     */
    protected $teachingHospitalName;

    /**
     * @var string
     * @MongoDB\String
     * @MongoDB\Index
     */
    protected $physicianProfileId;

    /**
     * @var string
     * @MongoDB\String
     */
    protected $physicianFirstName;

    /**
     * @var string
     * @MongoDB\String
     */
    protected $physicianMiddleName;

    /**
     * @var string
     * @MongoDB\String
     * @MongoDB\Index
     */
    protected $physicianLastName;

    /**
     * columns not explicity described in model
     * @var array
     * @MongoDB\Hash
     */
    protected $miscColumns;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }




    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set generalTransactionId
     *
     * @param int $generalTransactionId
     * @return self
     */
    public function setGeneralTransactionId($generalTransactionId)
    {
        $this->generalTransactionId = $generalTransactionId;
        return $this;
    }

    /**
     * Get generalTransactionId
     *
     * @return int $generalTransactionId
     */
    public function getGeneralTransactionId()
    {
        return $this->generalTransactionId;
    }

    /**
     * Set programYear
     *
     * @param int $programYear
     * @return self
     */
    public function setProgramYear($programYear)
    {
        $this->programYear = $programYear;
        return $this;
    }

    /**
     * Get programYear
     *
     * @return int $programYear
     */
    public function getProgramYear()
    {
        return $this->programYear;
    }

    /**
     * Set paymentPublicationDate
     *
     * @param \DateTime $paymentPublicationDate
     * @return self
     */
    public function setPaymentPublicationDate($paymentPublicationDate)
    {
        $this->paymentPublicationDate = $paymentPublicationDate;
        return $this;
    }

    /**
     * Get paymentPublicationDate
     *
     * @return \DateTime $paymentPublicationDate
     */
    public function getPaymentPublicationDate()
    {
        return $this->paymentPublicationDate;
    }

    /**
     * Set submittingApplicableManufacturerOrApplicableGpoName
     *
     * @param string $submittingApplicableManufacturerOrApplicableGpoName
     * @return self
     */
    public function setSubmittingApplicableManufacturerOrApplicableGpoName($submittingApplicableManufacturerOrApplicableGpoName)
    {
        $this->submittingApplicableManufacturerOrApplicableGpoName = $submittingApplicableManufacturerOrApplicableGpoName;
        return $this;
    }

    /**
     * Get submittingApplicableManufacturerOrApplicableGpoName
     *
     * @return string $submittingApplicableManufacturerOrApplicableGpoName
     */
    public function getSubmittingApplicableManufacturerOrApplicableGpoName()
    {
        return $this->submittingApplicableManufacturerOrApplicableGpoName;
    }

    /**
     * Set teachingHospitalName
     *
     * @param string $teachingHospitalName
     * @return self
     */
    public function setTeachingHospitalName($teachingHospitalName)
    {
        $this->teachingHospitalName = $teachingHospitalName;
        return $this;
    }

    /**
     * Get teachingHospitalName
     *
     * @return string $teachingHospitalName
     */
    public function getTeachingHospitalName()
    {
        return $this->teachingHospitalName;
    }

    /**
     * Set physicianProfileId
     *
     * @param string $physicianProfileId
     * @return self
     */
    public function setPhysicianProfileId($physicianProfileId)
    {
        $this->physicianProfileId = $physicianProfileId;
        return $this;
    }

    /**
     * Get physicianProfileId
     *
     * @return string $physicianProfileId
     */
    public function getPhysicianProfileId()
    {
        return $this->physicianProfileId;
    }

    /**
     * Set physicianFirstName
     *
     * @param string $physicianFirstName
     * @return self
     */
    public function setPhysicianFirstName($physicianFirstName)
    {
        $this->physicianFirstName = $physicianFirstName;
        return $this;
    }

    /**
     * Get physicianFirstName
     *
     * @return string $physicianFirstName
     */
    public function getPhysicianFirstName()
    {
        return $this->physicianFirstName;
    }

    /**
     * Set physicianMiddleName
     *
     * @param string $physicianMiddleName
     * @return self
     */
    public function setPhysicianMiddleName($physicianMiddleName)
    {
        $this->physicianMiddleName = $physicianMiddleName;
        return $this;
    }

    /**
     * Get physicianMiddleName
     *
     * @return string $physicianMiddleName
     */
    public function getPhysicianMiddleName()
    {
        return $this->physicianMiddleName;
    }

    /**
     * Set physicianLastName
     *
     * @param string $physicianLastName
     * @return self
     */
    public function setPhysicianLastName($physicianLastName)
    {
        $this->physicianLastName = $physicianLastName;
        return $this;
    }

    /**
     * Get physicianLastName
     *
     * @return string $physicianLastName
     */
    public function getPhysicianLastName()
    {
        return $this->physicianLastName;
    }

    /**
     * Set miscColumns
     *
     * @param array $miscColumns
     * @return self
     */
    public function setMiscColumns($miscColumns)
    {
        $this->miscColumns = $miscColumns;
        return $this;
    }

    /**
     * Get miscColumns
     *
     * @return array $miscColumns
     */
    public function getMiscColumns()
    {
        return $this->miscColumns;
    }

    /**
     * Set savedOn
     *
     * @param \DateTime $savedOn
     * @return self
     */
    public function setSavedOn($savedOn)
    {
        $this->savedOn = $savedOn;
        return $this;
    }

    /**
     * Get savedOn
     *
     * @return \DateTime $savedOn
     */
    public function getSavedOn()
    {
        return $this->savedOn;
    }

    /**
     * Set scraperJobId
     *
     * @param int $scraperJobId
     * @return self
     */
    public function setScraperJobId($scraperJobId)
    {
        $this->scraperJobId = $scraperJobId;
        return $this;
    }

    /**
     * Get scraperJobId
     *
     * @return int $scraperJobId
     */
    public function getScraperJobId()
    {
        return $this->scraperJobId;
    }

    /**
     * convert all properties to a hash array
     * @return array
     */
    public function toArray()
    {
        $vars = get_object_vars($this);
        $model = [];
        foreach ($vars as $property => $propertyValue) {
            if ($propertyValue instanceof \DateTime) {
                //convert to string
                $model[$this->toUnderscore($property)] = $propertyValue->format('m/d/Y');
            } else {
                $model[$this->toUnderscore($property)] = $propertyValue;
            }
        }
        return $model;
    }

    /**
     * convert camel cased string to underscored string
     *  return the new string
     *
     * @param string $string
     * @return string
     */
    protected function toUnderscore($string)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    }
}
