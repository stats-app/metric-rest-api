<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 3/1/15
 * Time: 12:21 PM
 */
include "../../vendor/autoload.php";
use Symfony\Component\HttpFoundation\Request;
use TomVerran\Stats\Storage\Database\MysqlConfiguration;
use TomVerran\Stats\Storage\DatabaseStorage;
header('Access-Control-Allow-Origin: *');

$app = new \Silex\Application;
$configuration = new MysqlConfiguration;
$configuration->setDatabase( 'metrics' )
    ->setHost( 'localhost' )
    ->setUsername( 'root' )
    ->setPassword( '' );

$request = Request::createFromGlobals();
$storage = new DatabaseStorage( $configuration );
$builder = new \TomVerran\MetricRestApi\ApplicationFactory( $storage, $app );
$builder->getApplication();
$app->run( $request );