<?php

namespace Tests\Hellofresh\DoctrineTutorial;

use Doctrine\Common\Cache;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\YamlDriver;
use Doctrine\ODM\MongoDB\Types\Type;
use Doctrine\MongoDB\Connection;

trait DocumentManagerTestCase
{
    /**
     * @return DocumentManager
     */
    public function getDm()
    {
        $params = [
            'server' => 'localhost',
            'database' => 'doctrine_tutorial',
            'metadata_paths' => [
                __DIR__ . '/../src/Menu/mapping',
            ],
        ];
        $cacheDir = __DIR__ . '/../cache';
        $config = new Configuration();

        $config->setProxyDir($cacheDir . '/Proxies');
        $config->setProxyNamespace('Proxies');
        $config->setHydratorDir($cacheDir . '/Hydrators');
        $config->setHydratorNamespace('Hydrators');
        $config->setDefaultDB($params['database']);
        $config->setMetadataDriverImpl(new YamlDriver($params['metadata_paths']));

        $connection = new Connection($params['server'],array(),$config);

        // Create instance
        return DocumentManager::create($connection, $config);
    }
}
