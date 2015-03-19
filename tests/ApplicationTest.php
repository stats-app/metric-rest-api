<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 3/19/15
 * Time: 7:12 PM
 */
namespace TomVerran\MetricRestApi;
use PHPUnit_Framework_TestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TomVerran\Stats\Metric;
use TomVerran\Stats\Storage\ArrayStorage;
use TomVerran\Stats\Storage\Storage;

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var Storage
     */
    private $storage;

    public function setUp()
    {
        $this->app = new Application;
        $this->storage = new ArrayStorage;

        $factory = new ApplicationFactory( $this->storage, $this->app );
        $factory->getApplication();
    }

    private function getRequest( $url, $getArgs = [] )
    {
        return new Request( $getArgs, [], [], [],[], ['REQUEST_URI' => $url] );
    }

    public function testListMetrics()
    {
        $this->storage->store( new Metric( 'cats', 0, 'number', 1 ) );
        $this->storage->store( new Metric( 'dogs', 0, 'number', 1 ) );

        $request = $this->getRequest( '/metrics/list' );
        $response = $this->app->handle( $request );

        $this->assertEquals( json_encode( ['cats', 'dogs'] ), $response->getContent() );
    }

    public function testGetSeries()
    {
        $this->storage->store( new Metric( 'cats', 0, 'number', 1 ) );
        $this->storage->store( new Metric( 'dogs', 0, 'number', 1 ) );

        $request = $this->getRequest( '/metrics/series', ['series' => ['cats', 'dogs']] );
        $response = $this->app->handle( $request );

        $expectedResponse = [['name' => 'cats', 'values' => [0]], ['name' => 'dogs', 'values' => [0]]];
        $this->assertEquals( json_encode( $expectedResponse ), $response->getContent() );
    }

    public function testGetSeriesWithTimeFilters()
    {
        $this->storage->store( new Metric( 'cats', 0, 'number', $ninety = date( 'U', strtotime( '1990-01-01' ) ) ) );
        $this->storage->store( new Metric( 'dogs', 0, 'number', $ninetyOne = date( 'U', strtotime( '1991-01-01' ) ) ) );

        $request = $this->getRequest( '/metrics/series', ['series' => ['cats', 'dogs'], 'from' => $ninety, 'to' => $ninety ] );
        $response = $this->app->handle( $request );

        $expectedResponse = [['name' => 'cats', 'values' => [0]], ['name' => 'dogs', 'values' => []]];
        $this->assertEquals( json_encode( $expectedResponse ), $response->getContent() );
    }

}