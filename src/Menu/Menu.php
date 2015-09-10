<?php

namespace Hellofresh\DoctrineTutorial\Menu;

use Hellofresh\DoctrineTutorial\Common\Collection;
use Hellofresh\DoctrineTutorial\Common\CollectionInterface;
use Hellofresh\DoctrineTutorial\Product\Product;

class Menu
{
    /**
     * @var string
     */
    protected $week;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var CollectionInterface  Recipe instances
     */
    protected $recipes;

    public function __construct()
    {
        $this->recipes = new Collection;
    }

    public function getWeek()
    {
        return $this->week;
    }

    public function setWeek($week)
    {
        $this->week = $week;
        return $this;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    public function addRecipe(Recipe $recipe)
    {
        $this->recipes->add($recipe);
        return $this;
    }

    public function removeRecipe(Recipe $recipe)
    {
        $this->recipes->removeElement($recipe);
        return $this;
    }

    public function getRecipes()
    {
        return $this->recipes;
    }
}
