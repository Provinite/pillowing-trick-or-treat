<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TrickOrTreatEventDAO extends aDAO {

    public function __construct() {
        parent::__construct();
        $this->entityClass = "TrickOrTreatEvent";
        $this->table = "trick_or_treat_events";
    }

    public function arrayFromEntity(iEntity $trickOrTreatEvent) {
        if (false) { $trickOrTreatEvent = new TrickOrTreatEvent(); } //ugly fix for intellisense

        return array(
            "id"            => $trickOrTreatEvent->getId(),
            "user_id"       => $trickOrTreatEvent->getUserId(),
            "date_time"     => $trickOrTreatEvent->getDatetime(),
            "win_loss"      => $trickOrTreatEvent->getWinLoss(),
            "ip_address"    => $trickOrTreatEvent->getIpAddress()
        );
    }

    /**
     * @param array $trickOrTreatEvent
     * @return TrickOrTreatEvent
     */
    public function entityFromArray(Array $trickOrTreatEvent) {
        $ret = new WinEvent();
        $ret->setId($trickOrTreatEvent['id']);
        $ret->setUserId($trickOrTreatEvent['user_id']);
        $ret->setDatetime($trickOrTreatEvent['date_time']);
        $ret->setIpAddress($trickOrTreatEvent['ip_address']);

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
            'win_loss' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
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