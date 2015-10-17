<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TrickOrTreatEvent implements iEntity {
    private $id;
    private $user_id;
    private $dateTime;
    private $winLoss;
    private $ipAddress;

    public function getIdField() {
        return "id";
    }

    /**
     * @param mixed $dateTime
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
    }

    /**
     * @return mixed
     */
    public function getDateTime()
    {
        return $this->dateTime;
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
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    public function getIpAddress() {
        return $this->ipAddress;
    }

    public function setIpAddress($ipAddress) {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @param string $winLoss
     * @throws InvalidArgumentException
     */
    public function setWinLoss($winLoss)
    {
        $winLoss = strtolower($winLoss);
        if ($winLoss !== "win" && $winLoss !== "loss" && $winLoss !== null) {
            throw new InvalidArgumentException("Argument winLoss must be element of {win, loss, NULL}");
        }
        $this->winLoss = $winLoss;
    }

    /**
     * @return mixed
     */
    public function getWinLoss()
    {
        return $this->winLoss;
    }


}