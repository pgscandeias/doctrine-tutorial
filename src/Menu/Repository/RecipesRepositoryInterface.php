<?php

namespace Hellofresh\DoctrineTutorial\Menu\Repository;

use Hellofresh\DoctrineTutorial\Menu\Recipe;

interface RecipesRepositoryInterface
{
    /**
     * @param  string $id
     * @return Recipe
     */
    public function find($id);
}
