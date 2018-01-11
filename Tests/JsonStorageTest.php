<?php

namespace Sfynx\CircuitBreakerBundle\Tests;

use \Phake;
use Sfynx\CircuitBreakerBundle\Storage\JsonStorage;

class JsonStorageTest extends \PHPUnit_Framework_TestCase
{
    protected $cacheDir;
    protected $cacheFactory;
    protected $jsonStorage;
    protected $factoryClient;

    public function setUp()
    {
        $this->cacheDir = "/tmp/";
        $this->cacheFactory = Phake::mock('Sfynx\CacheBundle\Manager\CacheFactory');
        $this->factoryClient = Phake::mock('Sfynx\CacheBundle\Manager\Client\FilecacheClient');
        Phake::when($this->factoryClient)->setPath(Phake::anyParameters())->thenReturn(
            true
        );
        Phake::when($this->cacheFactory)->getClient(Phake::anyParameters())->thenReturn(
            $this->factoryClient
        );
        $this->jsonStorage = new JsonStorage($this->cacheFactory);
        $this->jsonStorage->loadConfig($this->cacheDir);
    }

    public function testSaveStatus()
    {
        $this->jsonStorage->saveStatus('service-name',['some data']);
        Phake::verify($this->factoryClient, Phake::times(1))->set(Phake::anyParameters());
    }

    public function testLoadStatus()
    {
        $this->jsonStorage->loadStatus('service-name');
        Phake::verify($this->factoryClient, Phake::times(1))->get(Phake::anyParameters());
    }
}
