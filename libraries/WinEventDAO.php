<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WinEventDAO extends aDAO {

    public function __construct() {
        parent::__construct();
        $this->entityClass = "WinEvent";
        $this->table = "win_events";
    }

    public function arrayFromEntity(iEntity $winEvent) {
        if (false) { $winEvent = new WinEvent(); } //ugly fix for intellisense

        return array(
            "id"            => $winEvent->getId(),
            "user_id"       => $winEvent->getUserId(),
            "prize_id"      => $winEvent->getPrizeId(),
            "date_time"     => $winEvent->getDatetime(),
            "ip_address"    => $winEvent->getIpAddress()
        );
    }

    /**
     * @param array $winEvent
     * @return WinEvent
     */
    public function entityFromArray(Array $winEvent) {
        $ret = new WinEvent();
        $ret->setId($winEvent['id']);
        $ret->setUserId($winEvent['user_id']);
        $ret->setPrizeId($winEvent['prize_id']);
        $ret->setDatetime($winEvent['date_time']);
        $ret->setIpAddress($winEvent['ip_address']);

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
            'user_id' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false
            ),
            'prize_id' => array(
                'type' => 'INT',
                'constraint' => '9',
                'null' => false
            ),
            'date_time' => array(
                'type' => 'DATETIME',
                'null' => false
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true
            )
        );

        $this->ci->load->dbforge();
        $this->ci->dbforge->add_field($fields);
        $this->ci->dbforge->add_key('id', true);
        $this->ci->dbforge->create_table($this->table);

    }
}

/* end of file WinEventDAO.php */
/* location: libraries/WinEventDAO.php */