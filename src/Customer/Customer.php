<?php

namespace Hellofresh\DoctrineTutorial\Customer;

use Hellofresh\DoctrineTutorial\Common\Collection;
use Hellofresh\DoctrineTutorial\Common\CollectionInterface;

class Customer
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var CollectionInterface
     */
    protected $subscribedProducts;

    public function __construct()
    {
        $this->subscribedProducts = new Collection;
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

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function addSubscribedProduct(Product $product)
    {
        $this->subscribedProducts->add($product);
        return $this;
    }

    public function removeSubscribedProduct(Product $product)
    {
        $this->subscribedProducts->removeElement($product);
        return $this;
    }

    public function getSubscribedProducts()
    {
        return $this->subscribedProducts;
    }
}
