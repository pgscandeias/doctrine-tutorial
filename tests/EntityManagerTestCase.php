<?php

namespace Tests\Hellofresh\DoctrineTutorial;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

trait EntityManagerTestCase
{
    /**
     * @return EntityManager
     */
    public function getEm()
    {
        // Basic configuration
        $isDev = true;
        $config = [
             'driver'   => 'pdo_mysql',
             'host'     => 'localhost',
             'user'     => 'dev',
             'password' => 'dev',
             'dbname'   => 'doctrine_tutorial',
        ];

        // Paths to directories with entity metadata
        $metadata_paths = [
            __DIR__ . '/../src/Customer/mapping',
            __DIR__ . '/../src/Menu/mapping',
            __DIR__ . '/../src/Product/mapping',
        ];

        $configuration = Setup::createYAMLMetadataConfiguration(
            $metadata_paths,
            isset($isDev) ? $isDev : false
        );

        return EntityManager::create($config, $configuration);
    }
}
