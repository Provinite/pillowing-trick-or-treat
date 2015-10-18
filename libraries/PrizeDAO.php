<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PrizeDAO extends aDAO {

    public function __construct() {
        parent::__construct();
        $this->entityClass = "Prize";
        $this->table = "prizes";
    }

    public function arrayFromEntity(iEntity $prize) {
        if (false) { $prize = new Prize(); }
        return array(
            "id"            => $prize->getId(),
            "name"          => $prize->getName(),
            "image"         => $prize->getImage(),
            "description"   => $prize->getDescription(),
            "stock"         => $prize->getStock(),
            "initial_stock" => $prize->getInitialStock(),
            "weight"        => $prize->getWeight()
        );
    }

    /**
     * @param array $prize
     * @return Prize
     */
    public function entityFromArray(Array $prize) {
        $ret = new Prize();

        $ret->setId($prize['id']);
        $ret->setName($prize['name']);
        $ret->setImage($prize['image']);
        $ret->setDescription($prize['description']);
        $ret->setStock($prize['stock']);
        $ret->setInitialStock($prize['initial_stock']);
        $ret->setWeight($prize['weight']);

        return $ret;
    }

    public function generateSchema() {
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '9',
                'unsigned' => true,
                'auto_increment' => true
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
            'image' => array(
                'type' => 'VARCHAR',
                'constraint' => '350'
            ),
            'description' => array(
                'type' => 'VARCHAR',
                'constraint' => '500'
            ),
            'stock' => array(
                'type' => 'INT',
                'constraint' => '4'
            ),
            'initial_stock' => array(
                'type' => 'INT',
                'constraint' => '4'
            ),
            'weight' => array(
                'type' => 'FLOAT',
                'constraint' => '5,2',
                'default' => '1.0'
            )
        );

        $this->ci->load->dbforge();
        $this->ci->dbforge->add_field($fields);
        $this->ci->dbforge->add_key('id', true);
        $this->ci->dbforge->create_table($this->table);

    }
}