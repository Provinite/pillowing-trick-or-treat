<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class UserDAO {
    private $table = "users";
    private $ci;

    public function __construct() {
        $this->ci =& get_instance();
    }

    /**
     * @param User $user
     * @return User
     */
    public function save(User $user) {
        $uuid = $user->getUuid();
        $record = $this->ci->db
            ->select("*")
            ->from($this->table)
            ->where("uuid", $uuid)
            ->get();

        if ($record->num_rows() > 0) { //update rather than insert
            $existingUser = $this->userFromObject($record->row());
            $update = $this->patch($existingUser, $user);

            $this->ci->db
                ->where('uuid', $update->getUuid())
                ->set($this->arrayFromUser($update))
                ->update($this->table);
            return $update;
        } else { //inserting rather than updating
            $this->ci->db
                ->set($this->arrayFromUser($user))
                ->insert($this->table);
            return $user;
        }
    }

    /**
     * @param User $user
     */
    public function delete(User $user) {
        if ($this->exists($user)) {
            $this->ci->db
                ->where('uuid', $user->getUuid())
                ->delete($this->table);
        }
    }

    /**
     * @param $uuid
     * @return bool|User
     */
    public function get($uuid) {
        if ($uuid instanceof User) {
            $uuid = $uuid->getUuid();
        }
        $result = $this->ci->db
            ->select("*")
            ->from($this->table)
            ->where('uuid', $uuid)
            ->get();

        if ($result->num_rows() == 0) {
            return false;
        }

        return $this->userFromObject($result->row());
    }

    /**
     * @param $uuid
     * @return bool
     */
    public function exists($uuid) {
        if ($uuid instanceof User) {
            $uuid = $uuid->getUuid();
        }
        $count = $this->ci->db
            ->select('uuid')
            ->from($this->table)
            ->where('uuid', $uuid)
            ->get()
            ->num_rows();

        return ($count > 0);
    }

    /**
     * @param User $user
     * @return array
     */
    public function arrayFromUser(User $user) {
        return array(
            "uuid"          => $user->getUuid(),
            "access_token"  => $user->getAccessToken(),
            "refresh_token" => $user->getRefreshToken(),
            "auth_code"     => $user->getAuthCode(),
            "username"      => $user->getUsername(),
            "ip_address"    => $user->getIpAddress(),
            "token_issued"  => $user->getTokenIssued()
        );
    }

    /**
     * @param array $user
     * @return User
     */
    public function userFromArray(Array $user) {
        $ret = new User();
        $ret->setUuid($user['uuid']);
        $ret->setAccessToken($user['access_token']);
        $ret->setRefreshToken($user['refresh_token']);
        $ret->setAuthCode($user['auth_code']);
        $ret->setUsername($user['username']);
        $ret->setIpAddress($user['ip_address']);
        $ret->setTokenIssued($user['token_issued']);

        return $ret;
    }

    /**
     * @param $obj
     * @return User
     */
    public function userFromObject($obj) {
        $uArray = $this->arrayFromUser(new User());
        foreach ($uArray as $k => $v) {
            if (property_exists($obj, $k)) {
                $uArray[$k] = $obj->$k;
            }
        }
        return $this->userFromArray($uArray);
    }

    /**
     * @param User $user
     * @param User $patch
     * @return User
     */
    public function patch(User $user, User $patch) {

        $aPatch = $this->arrayFromUser($patch);
        $aUser = $this->arrayFromUser($user);
        $result = array();
        foreach ($aUser as $key => $value) {
            $result[$key] = ($aPatch[$key] !== null) ? $aPatch[$key] : $aUser[$key];
        }

        return $this->userFromArray($result);
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