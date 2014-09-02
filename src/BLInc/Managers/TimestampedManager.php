<?php

namespace BLInc\Managers;

use Doctrine\DBAL\Connection;

abstract class TimestampedManager implements ManagerInterface {
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $dbal;

    public function __construct(Connection $dbal) {
        $this->dbal = $dbal;
    }

    abstract public function getTable();

    public function find($id) {
        $data = $this->dbal->fetchAssoc($this->getFindOneQuery(), array('id' => $id));

        return is_array($data) ? $data : null;
    }

    protected function getFindOneQuery() {
        return sprintf('SELECT * FROM %s WHERE id = :id', $this->getTable());
    }

    public function findAll() {
        $rows = $this->dbal->fetchAll($this->getFindAllQuery());

        return $rows;
    }

    protected function getFindAllQuery() {
        return sprintf('SELECT * FROM %s', $this->getTable());
    }

    public function create(array $data) {
        $data = array_merge($data, array(
            'created_at' => date_create()->format('Y-m-d H:i:s'),
            'updated_at' => date_create()->format('Y-m-d H:i:s'),
        ));

        $this->dbal->insert($this->getTable(), $data);

        return $this->dbal->lastInsertId();
    }

    public function update($id, array $data) {
        $data = array_merge($data, array(
            'updated_at' => date_create()->format('Y-m-d H:i:s'),
        ));

        $this->dbal->update($this->getTable(), $data, array('id' => $id));

        // @TODO check modified rows
        return true;
    }

    public function delete($id) {
        $this->dbal->delete($this->getTable(), array('id' => $id));

        // @TODO check modified rows
        return true;
    }
}