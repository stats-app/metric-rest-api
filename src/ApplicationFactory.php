<?php
namespace TomVerran\MetricRestApi;
use DateTime;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use TomVerran\Stats\Storage\Storage;

/**
 * Created by PhpStorm.
 * User: tom
 * Date: 3/19/15
 * Time: 7:08 PM
 */

class ApplicationFactory
{
    public function __construct( Storage $storage, Application $app )
    {
        $this->app = $app;
        $this->storage = $storage;
    }

    private function parseDateArg( $arg )
    {
        if ( $arg ) {
            return DateTime::createFromFormat('U', $arg );
        }
        return null;
    }

    public function getApplication()
    {
        $this->app->get( 'metrics/list', function() {
            return new JsonResponse( $this->storage->getMetricNames() );
        } );

        /**
         * Retrieve a list of metrics
         */
        $this->app->get( 'metrics/series', function( Request $request ) {

            $response = [];
            foreach( $request->query->get( 'series', [] ) as $seriesName ) {

                $to = $this->parseDateArg( $request->query->get( 'to' ) );
                $from = $this->parseDateArg( $request->query->get( 'from' ) );
                $series = $this->storage->getMetricSeries( $seriesName, $from, $to );

                $response[] = [
                    'name' => $series->getName(),
                    'values' => $series->getValues()
                ];
            }
            return new JsonResponse( $response );
        } );
    }
}