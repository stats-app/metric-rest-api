<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 3/1/15
 * Time: 12:21 PM
 */
include "../vendor/autoload.php";
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TomVerran\Stats\Storage\Database\MysqlConfiguration;
use TomVerran\Stats\Storage\DatabaseStorage;


$app = new \Silex\Application;
$configuration = new MysqlConfiguration;
$configuration->setDatabase( 'metrics' )
    ->setHost( 'localhost' )
    ->setUsername( 'root' )
    ->setPassword( '' );

$storage = new DatabaseStorage( $configuration );

/**
 * Retrieve a list of metrics
 */
$app->get( 'metrics/list', function() use( $storage ) {
    return new JsonResponse( $storage->getMetricNames() );
} );

/**
 * Retrieve a list of metrics
 */
$app->get( 'metrics/series', function( Request $request ) use( $storage ) {
    $response = [];
    foreach( $request->query->get( 'series', [] ) as $seriesName ) {
        $series = $storage->getMetricSeries( $seriesName );
        $response[] = [
            'name' => $series->getName(),
            'values' => $series->getValues()
        ];
    }
    return new JsonResponse( $response );
} );

$app->run();