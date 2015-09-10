<?php
// tests/Menu/Repository/RecipesRepositoryTest.php

namespace Tests\Hellofresh\DoctrineTutorial\Menu\Repository;

use Hellofresh\DoctrineTutorial\Menu\Repository\RecipesRepository;
use Tests\Hellofresh\DoctrineTutorial\DocumentManagerTestCase;
use Tests\Hellofresh\DoctrineTutorial\BaseTestCase;

class RecipesRepositoryTest extends BaseTestCase
{
    use DocumentManagerTestCase;

    /**
     * @expectedException Hellofresh\DoctrineTutorial\Common\NotFoundException
     * @expectedExceptionMessage Recipe not found
     */
    public function testFindFailsWithNotFoundException()
    {
        $repository = new RecipesRepository($this->getDm());
        $repository->find(9999);
    }

    public function testFindReturnsRecipe()
    {
        $dm = $this->getDm();
        $this->loadFixtures('recipes', $dm);

        $repository = new RecipesRepository($dm);
        $this->assertInstanceOf(
            'Hellofresh\DoctrineTutorial\Menu\Recipe',
            $repository->find('55f13bd332668a28390041a7')
        );
    }
}
