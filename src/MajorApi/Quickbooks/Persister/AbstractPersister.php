<?php

namespace MajorApi\Quickbooks\Persister;

use MajorApi\Quickbooks\Parser\AbstractParser;

use Doctrine\DBAL\Connection;

use \DateTime,
    \Exception;

abstract class AbstractPersister
{

    /** @var Doctrine\DBAL\Connection */
    protected $postgres;

    /** @var MajorApi\Quickbooks\Parser\AbstractParser */
    protected $parser;

    /** @var integer */
    protected $applicationId = 0;

    /** @var array */
    protected $application;

    public function __construct(Connection $postgres, AbstractParser $parser, $applicationId)
    {
        $this->postgres = $postgres;
        $this->parser = $parser;
        $this->applicationId = (int)abs($applicationId);
    }

    public function persist()
    {
        $successfullyPersisted = true;

        try {
            $postgres = $this->postgres;
            $postgres->beginTransaction();

            // Load, initialize, and parse the XML.
            // This can throw an exception if the XML fails to parse properly.
            $container = $this->parser->load()->initialize()->parse();

            foreach ($container as $entity) {
                // Check if the entity already exists. If so, update it if we can, otherwise, insert if if we can.
                $existingEntity = $this->select($entity);

                if ($existingEntity) {
                    if ($this->hasUpdateParameters($entity)) {
                        $postgres->update($this->getTableName(), $this->getUpdateParameters($entity), ['id' => $existingEntity['id']]);
                        $this->postUpdateHook($existingEntity['id'], $entity);
                    }
                } else {
                    if ($this->hasInsertParameters($entity)) {
                        $postgres->insert($this->getTableName(), $this->getInsertParameters($entity));
                        $this->postInsertHook($entity);
                    }
                }
            }

            $postgres->commit();
        } catch (Exception $e) {
            // @todo Log the exception message so the user can see it.
            $postgres->rollback();
            $successfullyPersisted = false;
        }

        return $successfullyPersisted;
    }

    public function getParser()
    {
        return $this->parser;
    }

    public function getApplication()
    {
        if (!$this->application) {
            $query = "SELECT a.* FROM web_application a WHERE a.id = ?";

            $this->application = $this->postgres->fetchAssoc($query, [$this->applicationId]);
        }

        return $this->application;
    }

    public function getSelectQuery()
    {
        return '';
    }

    public function getSelectParameters(array $entity)
    {
        return [];
    }

    public function getInsertParameters(array $entity)
    {
        return [];
    }

    public function getUpdateParameters(array $entity)
    {
        return [];
    }

    public function hasSelectQuery()
    {
        return (strlen($this->getSelectQuery()) > 0);
    }

    public function hasInsertParameters(array $entity)
    {
        return (count($this->getInsertParameters($entity)) > 0);
    }

    public function hasUpdateParameters(array $entity)
    {
        return (count($this->getUpdateParameters($entity)) > 0);
    }

    abstract public function getTableName();

    protected function postInsertHook(array $entity)
    {
        return true;
    }

    protected function postUpdateHook($id, array $entity)
    {
        return true;
    }

    private function select(array $entity)
    {
        // Attempt to select an existing entity if the persister in question has a SELECT query to do so.
        if ($this->hasSelectQuery()) {
            $entity = $this->postgres->fetchAssoc($this->getSelectQuery(), $this->getSelectParameters($entity));

            return $entity;
        }

        return false;
    }

}
