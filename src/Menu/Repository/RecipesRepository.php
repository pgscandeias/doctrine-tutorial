<?php
// src/Menu/Repository/RecipesRepository.php

namespace Hellofresh\DoctrineTutorial\Menu\Repository;

use Doctrine\ODM\MongoDB\DocumentManager;
use Hellofresh\DoctrineTutorial\Common\NotFoundException;
use Hellofresh\DoctrineTutorial\Menu\Recipe;

class RecipesRepository implements RecipesRepositoryInterface
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * @param  string $id
     * @return Recipe
     * @throws NotFoundException If no Recipe found
     */
    public function find($id)
    {
        $recipe = $this
            ->dm
            ->find('Hellofresh\DoctrineTutorial\Menu\Recipe', $id)
        ;

        if (!$recipe) {
            throw new NotFoundException("Recipe not found");
        }

        return $recipe;
    }
}
