<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WinEvent implements iEntity {
    private $id;
    private $userId;
    private $prizeId;
    private $datetime;
    private $ipAddress;

    public function __construct($now = false) {
        if ($now) {
            $this->setDatetime(
                date("Y-m-d H:m:s")
            );
        }
    }

    public function getIdField()
    {
        return "id";
    }

    /**
     * @param mixed $datetime
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * @return mixed
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param mixed $prizeId
     */
    public function setPrizeId($prizeId)
    {
        $this->prizeId = $prizeId;
    }

    /**
     * @return mixed
     */
    public function getPrizeId()
    {
        return $this->prizeId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }
}