<?php

namespace EIdeas\OpenPayments\ScraperBundle\Scraper\Job;

use EIdeas\OpenPayments\ScraperBundle\Document\DocumentManager;
use EIdeas\OpenPayments\ScraperBundle\Document\Model\DoctorPayment as DoctorPaymentModel;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Class DoctorPaymentProcessor
 * Handles adding and updating payment records
 *
 * @package EIdeas\OpenPayments\ScraperBundle\Scraper\Job
 */
class DoctorPaymentProcessor {

    /**
     * these are the columns that can be set in the model
     *   key = field_name, value = column id as can be found in the record data
     *  Everything else will be stored as a hash in mongo
     *
     * @var array
     */
    private $importantColumns = array(
        'created_at'                  => 'created_at',
        'updated_at'                  => 'updated_at',
        'program_year'                => 0,
        'payment_publication_date'    => 0,
        'submitting_applicable_manufacturer_or_applicable_gpo_name' => 0,
        'teaching_hospital_name'      => 0,
        'physician_profile_id'        => 0,
        'physician_first_name'        => 0,
        'physician_middle_name'       => 0,
        'physician_last_name'         => 0,
    );

    /**
     * columns to ignore
     * @var array
     */
    private $skipColumns = array(
        'meta', 'position', 'created_meta', 'updated_meta', 'sid'
    );

    /**
     * point the op id to the column name, so in the db we save the column name for readability
     * @var array
     */
    private $extraColumns = array();

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var DocumentRepository
     */
    private $paymentRepository;

    /**
     * @var DocumentRepository
     */
    private $columnRepository;

    /**
     * num of records added or updated
     * @var int
     */
    private $recordCount = 0;

    /**
     * look this up once and re-use
     * @var int
     */
    private $idColumnId = 0;

    private $qb;

    /**
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
        $this->paymentRepository = $documentManager->getRepository('doctor_payment');
        $this->columnRepository = $documentManager->getRepository('op_column');

        $this->qb = $this->paymentRepository->createQueryBuilder('dp');
        $this->qb->where('dp.generalTransactionId = :transactionId');
    }

    /**
     * Look for existing, update or add new, or do nothing
     * @param array $paymentRecord
     * @param int $jobId
     * @throws PaymentProcessorException    *if payment record data is invalid
     * @throws \RuntimeException            *if id for column id can't be found - loop should quit
     */
    public function save(Array $paymentRecord, $jobId)
    {
        $this->setup();
        if (!$this->recordExists($paymentRecord, $jobId)) {
            //add new
            $doctorPayment = $this->documentManager->getModel('doctor_payment');
            $this->setFields($doctorPayment, $paymentRecord, $jobId);
        }
    }

    /**
     * Check if record already exists, if so, check if needs to be updated
     *
     * @param array $paymentRecord
     * @param int $jobId
     * @return bool
     * @throws PaymentProcessorException
     */
    private function recordExists(Array $paymentRecord, $jobId)
    {
        if (empty($paymentRecord[$this->idColumnId])) {
            throw new PaymentProcessorException('Missing transaction id column');
        }
        if (empty($paymentRecord['updated_at'])) {
            throw new PaymentProcessorException('Missing updated at information');
        }
        $transactionId = $paymentRecord[$this->idColumnId];

        /**
         * @var \EIdeas\OpenPayments\ScraperBundle\Document\Model\DoctorPayment $doctorPayment
         */
        $doctorPayment = $this->paymentRepository->findOneBy(['generalTransactionId' => (int)$transactionId]);
        if ($doctorPayment) {
            if ($paymentRecord['updated_at'] > $doctorPayment->getUpdatedAt()->getTimestamp()) {
                $this->setFields($doctorPayment, $paymentRecord, $jobId);
            }
            return true;
        }

        return false;
    }

    /**
     * @throws \RuntimeException
     */
    private function setup()
    {
        if ($this->idColumnId == 0) {
            /**
             * @var \EIdeas\OpenPayments\ScraperBundle\Document\Model\OpColumn $column
             */
            $column = $this->columnRepository->findOneBy(['fieldName' => 'general_transaction_id']);
            if ($column) {
                $this->idColumnId = $column->getOpId();
            } else {
                throw new \RuntimeException('Could not determine id for column, general transaction id');
            }

            //setup remaining column id's, but not as important as the transaction id
            foreach ($this->importantColumns as $fieldName => $opId) {
                /**
                 * @var \EIdeas\OpenPayments\ScraperBundle\Document\Model\OpColumn $opColumn
                 */
                $opColumn = $this->columnRepository->findOneBy(['fieldName' => $fieldName]);
                if ($opColumn) {
                    $this->importantColumns[$fieldName] = $opColumn->getOpId();
                }
            }
        }
    }

    /**
     * total number of records added and updated
     * @return int
     */
    public function getRecordCount()
    {
        return $this->recordCount;
    }

    /**
     * sets all fields and marks the model as ready for saving come the saveAll() call
     *
     * @param DoctorPaymentModel $doctorPayment
     * @param array $paymentRecord
     * @param int $jobId
     */
    private function setFields(DoctorPaymentModel &$doctorPayment, Array $paymentRecord, $jobId)
    {
        $doctorPayment->setGeneralTransactionId($paymentRecord[$this->idColumnId])
                      ->setSavedOn(date('Y-m-d H:i:s'))
                      ->setScraperJobId($jobId);
        unset($paymentRecord[$this->idColumnId]);
        foreach ($this->importantColumns as $fieldId => $columnId) {
            if ($columnId !== 0 && !empty($paymentRecord[$columnId])) {
                $methodName = 'set' . $this->camelCase($fieldId);
                $doctorPayment->$methodName($paymentRecord[$columnId]);
                unset($paymentRecord[$columnId]);
            }
        }
        $miscColumns = [];
        foreach ($paymentRecord as $fieldId => $fieldValue) {
            if (!in_array($fieldId, $this->skipColumns)) {
                if (!isset($this->extraColumns[$fieldId])) {
                    //fieldId in this case is the numeric op identifier
                    $column = $this->columnRepository->findOneBy(['opId' => $fieldId]);
                    if ($column) {
                        $this->extraColumns[$fieldId] = $column->getFieldName();
                    } else {
                        //can't find, so just leave it as the fieldId
                        $this->extraColumns[$fieldId] = $fieldId;
                    }
                }
                $miscColumns[$this->extraColumns[$fieldId]] = $fieldValue;
            }
        }
        $doctorPayment->setMiscColumns($miscColumns);

        $this->documentManager->save($doctorPayment);
        $this->recordCount++;
    }

    /**
     * Make an underscored string camel case
     * @param string $string
     * @return string
     */
    private function camelCase($string)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }

}