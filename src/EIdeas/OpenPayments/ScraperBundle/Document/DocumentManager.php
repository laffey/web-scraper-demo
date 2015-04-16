<?php

namespace EIdeas\OpenPayments\ScraperBundle\Document;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\ODM\MongoDB\DocumentManager as DoctrineDm;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Class DocumentManager
 * custom wrapper around the doctrine manager, such that I can create a class map
 *   of document classes
 *
 * @package EIdeas\OpenPayments\ScraperBundle\Document
 */
class DocumentManager {

    const NS = 'EIdeas\OpenPayments\ScraperBundle\Document\Model\\';

    private $documentMap = array(
        'scraper_job'       => self::NS . 'ScraperJob',
        'raw_response'      => self::NS . 'RawResponse',
        'op_column'         => self::NS . 'OpColumn',
        'doctor_payment'    => self::NS . 'DoctorPayment',
    );

    /**
     * @var DoctrineDm
     */
    private $dm;


    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->dm = $managerRegistry->getManager();
    }

    /**
     * init and return an empty model
     * @param string $type
     * @return Model\ModelInterface
     * @throws DocumentRuntimeException
     */
    public function getModel($type)
    {
        $this->checkType($type);
        return new $this->documentMap[$type]();
    }

    /**
     * @param string $type
     * @param int $id
     * @return Model\ModelInterface || null
     * @throws \Doctrine\ODM\MongoDB\LockException
     * @throws DocumentRuntimeException
     */
    public function getById($type, $id)
    {
        $this->checkType($type);
        $repository = $this->dm->getRepository($this->documentMap[$type]);
        return $repository->find($id);
    }

    /**
     * @param Model\ModelInterface $model
     */
    public function save(Model\ModelInterface $model)
    {
        if (empty($model->getId())) {
            $this->dm->persist($model);
        }
        $this->dm->flush();
    }

    /**
     * when handling large amounts of models, delay the flush() call
     *  but notify doctrine of a new model
     *
     * @param Model\ModelInterface $model
     */
    public function delaySave(Model\ModelInterface $model)
    {
        if (empty($model->getId())) {
            $this->dm->persist($model);
        }
    }

    /**
     * if used delaySave or delayRemove,
     *  or retrieved models from the db, and made changes,
     *  call this at the end to do a final db flush
     */
    public function saveAll()
    {
        $this->dm->flush();
    }

    /**
     * delete a model from the db
     * @param Model\ModelInterface $model
     */
    public function remove(Model\ModelInterface $model)
    {
        $this->dm->remove($model);
        $this->dm->flush();
    }

    /**
     * if deleting many models, use this, with a final saveAll() call
     * @param Model\ModelInterface $model
     */
    public function delayRemove(Model\ModelInterface $model)
    {
        $this->dm->remove($model);
    }

    /**
     * Use this to make calls directly on the repository for given type
     * @param $type
     * @return DocumentRepository
     * @throws DocumentRuntimeException
     */
    public function getRepository($type)
    {
        $this->checkType($type);
        return $this->dm->getRepository($this->documentMap[$type]);
    }

    /**
     * validate the type, throw an exception if it is invalid
     * @param string $type
     * @throws DocumentRuntimeException
     */
    private function checkType($type)
    {
        if (!array_key_exists($type, $this->documentMap)) {
            throw new DocumentRuntimeException("Invalid type given: " . $type);
        }
    }

}