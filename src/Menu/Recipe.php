<?php

namespace Hellofresh\DoctrineTutorial\Menu;

use Hellofresh\DoctrineTutorial\Common\Collection;
use Hellofresh\DoctrineTutorial\Common\CollectionInterface;

class Recipe
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var CollectionInterface  Step instances
     */
    protected $steps;

    public function __construct()
    {
        $this->steps = new Collection;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
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

    public function addStep(Step $step)
    {
        $this->steps->add($step);
        return $this;
    }

    public function removeStep(Step $step)
    {
        $this->steps->removeElement($step);
        return $this;
    }

    public function getSteps()
    {
        return $this->steps;
    }
}
