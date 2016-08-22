<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 'On');
use Doctrine\ORM\Tools\Setup;
require_once "vendor/autoload.php";
require_once "events/eventListener.php";
// Create a simple "default" Doctrine ORM configuration for XML Mapping
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), $isDevMode);
// or if you prefer yaml or annotations
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);
// database configuration parameters
$conn = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'synry63_drinkapp',
    'password' => '9.%3g^4C-bJg',
    'dbname'   => 'synry63_drinkapp',
    'charset' => 'utf8'
);
// obtaining the entity manager

$eventManager = new \Doctrine\Common\EventManager();
$eventManager->addEventListener(array(\Doctrine\ORM\Events::postPersist,\Doctrine\ORM\Events::postFlush), new EventListener());
$entityManager = \Doctrine\ORM\EntityManager::create($conn, $config,$eventManager);