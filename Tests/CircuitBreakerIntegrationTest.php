<?php

namespace Sfynx\CircuitBreakerBundle\Tests;

use \Phake;
use Sfynx\CacheBundle\Manager\CacheFactory;
use Sfynx\CacheBundle\Manager\Client\FilecacheClient;
use Sfynx\CircuitBreakerBundle\CircuitBreaker\CircuitBreaker;
use Sfynx\CircuitBreakerBundle\CircuitBreaker\ServiceConfiguration;
use Sfynx\CircuitBreakerBundle\Storage\JsonStorage;

class CircuitBreakerIntegrationTest extends \PHPUnit_Framework_TestCase
{
    protected $cb1;
    protected $cb2;

    protected $storage1;
    protected $storage2;

    protected $cacheFactory;
    protected $cacheClient;

    protected $serviceConfig;
    protected $cacheDir;
    protected $maxFailure;
    protected $resetTime;

    protected $serviceName;

    public function setUp()
    {
        $this->serviceName = "my-service";
        $this->cacheDir = '/var/www/';
        $this->maxFailure = 10;
        $this->resetTime = 4;
        $this->cacheClient = new FilecacheClient();
        $this->cacheFactory = new CacheFactory($this->cacheClient);
        $this->storage1 = new JsonStorage($this->cacheFactory);
        $this->storage2 = new JsonStorage($this->cacheFactory);
        $this->storage1->loadConfig($this->cacheDir);
        $this->storage2->loadConfig($this->cacheDir);
        $this->cb1 = new CircuitBreaker($this->storage1);
        $this->cb2 = new CircuitBreaker($this->storage2);
        $this->serviceConfig = new ServiceConfiguration($this->maxFailure, $this->resetTime);
        $this->cb1->registerService($this->serviceName, $this->serviceConfig);
        $this->cb2->registerService($this->serviceName, $this->serviceConfig);
    }

    public function tearDown()
    {
        $this->cacheClient->clear($this->serviceName);
    }

    public function testWith2CBOnSameService()
    {
        $this->assertTrue($this->cb1->isAvailable($this->serviceName));
        $this->assertTrue($this->cb2->isAvailable($this->serviceName));


        //fail with cb1 and cb2 and see the same count on serviceStatus
        $this->cb1->isAvailable($this->serviceName);//isAvailable load cache to update count value
        $this->cb1->reportFailure($this->serviceName);
        $this->cb2->isAvailable($this->serviceName);//isAvailable load cache to update count value
        $this->cb2->reportFailure($this->serviceName);

        $status1 = $this->storage1->loadStatus($this->serviceName);
        $status2 = $this->storage2->loadStatus($this->serviceName);
        $this->assertEquals($status1, $status2);
        $this->assertEquals(2, $status1['count']);
        $this->assertEquals(2, $status2['count']);

        //make unavailable by cb1
        for($i = 0; $i < $this->maxFailure+2; $i++) {
            $this->cb1->isAvailable($this->serviceName);//isAvailable load cache to update count value
            $this->cb1->reportFailure($this->serviceName);
        }

        //then try to access with cb2 and cb1 before resetTime
        $this->assertFalse($this->cb1->isAvailable($this->serviceName));
        $this->assertFalse($this->cb2->isAvailable($this->serviceName));

        //wait more than resetTime
        sleep($this->resetTime+1);

        //try to access with cb1 (true) and cb2(false)
        $this->assertTrue($this->cb1->isAvailable($this->serviceName));
        $this->assertFalse($this->cb2->isAvailable($this->serviceName));
    }
}
