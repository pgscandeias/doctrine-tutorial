<?php

namespace Hellofresh\DoctrineTutorial\Product;

class Product
{
    /**
     * @var id
     */
    protected $id;

    /**
     * @var string
     */
    protected $handle;

    /**
     * @var string
     */
    protected $name;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getHandle()
    {
        return $this->handle;
    }

    public function setHandle($handle)
    {
        $this->handle = $handle;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
