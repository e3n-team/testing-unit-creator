<?php

use App\Kernel;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
    require dirname(__DIR__) . '/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

$kernel = new Kernel('test', true);
$kernel->boot();

/** @var EntityManager $entityManager */
$entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
$metadata      = $entityManager->getMetadataFactory()->getAllMetadata();
$schemaTool    = new SchemaTool($entityManager);

$schemaTool->dropSchema($metadata);
$schemaTool->createSchema($metadata);

$kernel->shutdown();
