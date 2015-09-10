<?php

namespace Tests\Hellofresh\DoctrineTutorial;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ODM\MongoDB\DocumentManager;
use Nelmio\Alice\Fixtures;
use Symfony\Component\Yaml\Yaml;

class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Load fixtures from a file in the tests/fixtures directory.
     * This uses Nelmio's Alice package:
     * @see    https://github.com/nelmio/alice
     *
     * @param  string        $fixture Name of the YAML fixture to load
     * @param  ObjectManager $om Object manager to use
     */
    public static function loadFixtures($fixture, ObjectManager $om)
    {
        // Read and cache
        $yamlPath = sprintf('%s/Fixtures/%s.yml', __DIR__, $fixture);

        // Load fixtures
        Fixtures::load($yamlPath, $om);
    }
}
