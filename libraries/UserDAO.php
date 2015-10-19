<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserDAO extends aDAO {

    public function __construct() {
        parent::__construct();
        $this->entityClass = "User";
        $this->table = "users";
    }

    public function arrayFromEntity(iEntity $user) {
        if (false) { $user = new User(); } //ugly fix for intellisense

        return array(
            "uuid"          => $user->getUuid(),
            "access_token"  => $user->getAccessToken(),
            "refresh_token" => $user->getRefreshToken(),
            "auth_code"     => $user->getAuthCode(),
            "username"      => $user->getUsername(),
            "ip_address"    => $user->getIpAddress(),
            "token_issued"  => $user->getTokenIssued(),
            'icon'          => $user->getIcon()
        );
    }

    /**
     * @param array $user
     * @return User
     */
    public function entityFromArray(Array $user) {
        $ret = new User();
        $ret->setUuid($user['uuid']);
        $ret->setAccessToken($user['access_token']);
        $ret->setRefreshToken($user['refresh_token']);
        $ret->setAuthCode($user['auth_code']);
        $ret->setUsername($user['username']);
        $ret->setIpAddress($user['ip_address']);
        $ret->setTokenIssued($user['token_issued']);
        $ret->setIcon($user['icon']);

        return $ret;
    }


    public function generateSchema() {
        $fields = array(
            'uuid' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true
            ),
            'access_token' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true
            ),
            'refresh_token' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true
            ),
            'auth_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true
            ),
            'token_issued' => array(
                'type' => 'DATETIME',
                'null' => true
            ),
            'icon' => array(
                'type' => 'VARCHAR',
                'constraint' => '300',
                'null' => true
            )
        );

        $this->ci->load->dbforge();
        $this->ci->dbforge->add_field($fields);
        $this->ci->dbforge->add_key('uuid', true);
        $this->ci->dbforge->create_table($this->table);

    }
}

/* end of file UserDAO.php */
/* location: libraries/UserDAO.php */