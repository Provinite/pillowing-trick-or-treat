<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Prize implements iEntity {
    private $id = null;
    private $name = null;
    private $image = null;
    private $description = null;
    private $stock = null;
    private $initialStock = null;
    private $weight = null;

    public function getIdField() {
        return "id";
    }
    /**
     * @param null $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return null
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param null $initialStock
     */
    public function setInitialStock($initialStock)
    {
        $this->initialStock = $initialStock;
    }

    /**
     * @return null
     */
    public function getInitialStock()
    {
        return $this->initialStock;
    }

    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $stock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    /**
     * @return null
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @param null $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return null
     */
    public function getWeight()
    {
        return $this->weight;
    }


}