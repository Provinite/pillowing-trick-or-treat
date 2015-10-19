<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

abstract class aDAO {
    protected $table;
    protected $entityClass;
    protected $ci;

    public function __construct() {
        $this->ci =& get_instance();
    }

    public function __call($name, $args) {
        //$dao->get[First[N]]With([Variable[Not](Equals|Contains|StartsWith|EndsWith|Between)][AND|OR])*
        $pattern = '/get((?:First)?)(\d*)?(?:With|Having|Where)(.*)/';//([a-zA-Z0-9_]+?)(Not)?(StartsWith|EndsWith|Like|Contains|Equals|Is)(And|Or|$)?/';
        $pattern2 = '/([a-zA-Z0-9_]+?)(Not)?(StartsWith|EndsWith|Like|Contains|Equals|Is|In|LessThan|GreaterThan)(And|Or|$)?/';
        $pattern3 = '/Order(Desc|Asc)By([a-zA-Z0-9_]+)/';
        $matches = array();
        $matches2 = array();
        $matches3 = array();
        $result = preg_match_all($pattern, $name, $matches, PREG_SET_ORDER);
        //echo "<pre>";
        //echo "Result: $result\n\n";
        //print_r($matches);


        //echo "\n\nPattern 2:\n";
        $query = $matches[0][3];
        $result = preg_match_all($pattern2, $query, $matches2, PREG_SET_ORDER);
        //echo "Result: $result\n\n";
        //print_r($matches2);

        $result3 = preg_match_all($pattern3, $query, $matches3, PREG_SET_ORDER);

        $i = 0;

        //echo "SELECT * FROM " . $this->table . " WHERE ";

        $lastComparison = "";

        foreach ($matches2 as $condition) {
            $varName = $condition[1];
            $not = $condition[2] ? true : false;
            $comparator = strtoupper($condition[3]);
            $nextComparison=strtoupper($condition[4]);

            if ($lastComparison == "AND" || $lastComparison == "") {
                if ($comparator == "LIKE" | $comparator == "CONTAINS") {
                    if ($not) {
                        $this->ci->db->not_like($varName, $args[$i]);
                    } else {
                        $this->ci->db->like($varName, $args[$i]);
                    }
                } elseif ($comparator == "IS" || $comparator == "EQUALS") {
                    if ($not) {
                        $this->ci->db->where($varName . ' !=', $args[$i]);
                    } else {
                        $this->ci->db->where($varName, $args[$i]);
                    }
                } elseif ($comparator == "STARTSWITH") {
                    if ($not) {
                        $this->ci->db->not_like($varName, $args[$i], 'after');
                    } else {
                        $this->ci->db->like($varName, $args[$i], 'after');
                    }
                } elseif ($comparator == "ENDSWITH") {
                    if ($not) {
                        $this->ci->db->not_like($varName, $args[$i], 'before');
                    } else {
                        $this->ci->db->like($varName, $args[$i], 'before');
                    }
                } elseif ($comparator == "IN") {
                    if ($not) {
                        $this->ci->db->where_not_in($varName, $args[$i]);
                    } else {
                        $this->ci->db->where_in($varName, $args[$i]);
                    }
                } elseif ($comparator == "LESSTHAN") {
                    if ($not) {
                        $this->ci->db->where_not($varName . ' <', $args[$i]);
                    } else {
                        $this->ci->db->where($varName . ' <', $args[$i]);
                    }
                } elseif ($comparator == "GREATERTHAN") {
                    if ($not) {
                        $this->ci->db->where_not($varName . ' >', $args[$i]);
                    } else {
                        $this->ci->db->where($varName . ' >', $args[$i]);
                    }
                }
            } elseif ($lastComparison == "OR") {
                if ($comparator == "LIKE" | $comparator == "CONTAINS") {
                    if ($not) {
                        $this->ci->db->or_not_like($varName, $args[$i]);
                    } else {
                        $this->ci->db->or_like($varName, $args[$i]);
                    }
                } elseif ($comparator == "IS" || $comparator == "EQUALS") {
                    if ($not) {
                        $this->ci->db->or_where($varName . ' !=', $args[$i]);
                    } else {
                        $this->ci->db->or_where($varName, $args[$i]);
                    }
                } elseif ($comparator == "STARTSWITH") {
                    if ($not) {
                        $this->ci->db->or_not_like($varName, $args[$i], 'after');
                    } else {
                        $this->ci->db->or_like($varName, $args[$i], 'after');
                    }
                } elseif ($comparator == "ENDSWITH") {
                    if ($not) {
                        $this->ci->db->or_not_like($varName, $args[$i], 'before');
                    } else {
                        $this->ci->db->or_like($varName, $args[$i], 'before');
                    }
                } elseif ($comparator == "IN") {
                    if ($not) {
                        $this->ci->db->or_where_not_in($varName, $args[$i]);
                    } else {
                        $this->ci->db->or_where_in($varName, $args[$i]);
                    }
                } elseif ($comparator == "LESSTHAN") {
                    if ($not) {
                        $this->ci->db->or_where_not($varName . ' <', $args[$i]);
                    } else {
                        $this->ci->db->or_where($varName . ' <', $args[$i]);
                    }
                } elseif ($comparator == "GREATERTHAN") {
                    if ($not) {
                        $this->ci->db->or_where_not($varName . ' >', $args[$i]);
                    } else {
                        $this->ci->db->or_where($varName . ' >', $args[$i]);
                    }
                }
            }

            $lastComparison = $nextComparison;
            $i++;
        }

        if ($result3) {
            $matches3 = $matches3[0];
            if ($matches3[1] == "") {
                $matches3[1] = "ASC";
            }
            $matches3[1] = strtoupper($matches3[1]);
            $this->ci->db->order_by($matches3[2], $matches3[1]);
        }

        if ($matches[0][1] === "First" && $matches[0][2] === "") {
            $this->ci->db->limit(1);
        } elseif ($matches[0][1] === "First" && $matches[0][2] !== "") {
            $this->ci->db->limit($matches[0][2]);
        }

        $result = $this->ci->db->get($this->table);
        $return = array();
        foreach ($result->result() as $row) {
            $return[] = $this->entityFromObject($row);
        }

        return $return;
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