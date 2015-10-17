<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

abstract class aDAO {
    protected $table;
    protected $entityClass;
    protected $ci;

    public function __construct() {
        $this->ci =& get_instance();
    }

    public function save(iEntity $entity) {
        $id = $entity->getId();
        $record = $this->ci->db
            ->select("*")
            ->from($this->table)
            ->where($entity->getIdField(), $id)
            ->get();

        if ($record->num_rows() > 0) { //update rather than insert
            $existingEntity = $this->entityFromObject($record->row());
            $update = $this->patch($existingEntity, $entity);

            $this->ci->db
                ->where('uuid', $update->getUuid())
                ->set($this->arrayFromEntity($update))
                ->update($this->table);
            return $update;
        } else { //inserting rather than updating
            $this->ci->db
                ->set($this->arrayFromEntity($entity))
                ->insert($this->table);
            return $entity;
        }
    }

    public function delete(iEntity $entity) {
        if ($this->exists($entity)) {
            $this->ci->db
                ->where($entity->getIdField(), $entity->getId())
                ->delete($this->table);
        }
    }

    public function get($id) {
        if ($id instanceof iEntity) {
            $id = $id->getId();
        }

        $idCol = new $this->entityClass;
        $idCol = $idCol->getIdField();

        $result = $this->ci->db
            ->select("*")
            ->from($this->table)
            ->where($idCol, $id)
            ->get();

        if ($result->num_rows() == 0) {
            return false;
        }

        return $this->entityFromObject($result->row());
    }

    public function exists($id) {
        if ($id instanceof iEntity) {
            $id = $id->getId();
        }

        $idCol = new $this->entityClass;
        $idCol = $idCol->getIdField();

        $count = $this->ci->db
            ->select('id')
            ->from($this->table)
            ->where($idCol, $id)
            ->get()
            ->num_rows();

        return ($count > 0);
    }

    public function patch(iEntity $entity, iEntity $patch) {
        $aPatch = $this->arrayFromEntity($patch);
        $aEntity = $this->arrayFromEntity($entity);
        $result = array();
        foreach ($aEntity as $key => $value) {
            $result[$key] = ($aPatch[$key] !== null) ? $aPatch[$key] : $aEntity[$key];
        }

        return $this->entityFromArray($result);
    }

    abstract public function entityFromArray(Array $eArray);
    abstract public function arrayFromEntity(iEntity $entity);

    public final function entityFromObject($obj) {
        $uArray = $this->arrayFromEntity(new $this->entityClass);
        foreach ($uArray as $k => $v) {
            if (property_exists($obj, $k)) {
                $uArray[$k] = $obj->$k;
            }
        }
        return $this->entityFromArray($uArray);
    }

    abstract public function generateSchema();
} 