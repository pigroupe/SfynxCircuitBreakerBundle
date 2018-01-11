<?php

namespace Sfynx\CircuitBreakerBundle\Tests;

use Sfynx\CircuitBreakerBundle\CircuitBreaker\CircuitBreaker;
use \Phake;
use Sfynx\CircuitBreakerBundle\CircuitBreaker\ServiceConfiguration;
use Sfynx\CircuitBreakerBundle\Generalisation\CircuitBreakerInterface;

class CircuitBreakerTest extends \PHPUnit_Framework_TestCase
{
    protected $circuitBreaker;
    protected $storage;
    protected $serviceName;
    protected $maxFailure = 10;
    protected $resetTime = 4;

    public function setUp()
    {
        $this->serviceName = 'my_service';
        $this->storage = Phake::mock('Sfynx\CircuitBreakerBundle\Storage\JsonStorage');
        $this->circuitBreaker = new CircuitBreaker($this->storage);

        //setup config
        $this->circuitBreaker->registerService($this->serviceName, new ServiceConfiguration($this->maxFailure, $this->resetTime));
    }

    public function testInterface()
    {
        $this->assertTrue($this->circuitBreaker instanceOf CircuitBreakerInterface);
    }

    public function testAvailableCBDefault()
    {
        $this->assertTrue($this->circuitBreaker->isAvailable($this->serviceName));

        return $this->circuitBreaker;
    }

    /**
     * reports failure until cb open
     *
     * @depends testAvailableCBDefault
     */
    public function testOpenCB($cb)
    {
        for($i = 0; $i < $this->maxFailure+2; $i++) {
            $cb->reportFailure($this->serviceName);
        }
        Phake::verify($cb->getStorage(), Phake::times($this->maxFailure+2))->saveStatus(Phake::anyParameters());
        $this->assertFalse($cb->isAvailable($this->serviceName));
        $this->assertEquals($this->maxFailure, $cb->getStatus($this->serviceName)->getCount());

        return $cb;
    }

    /**
     * after opening, waits resetTime and reports failure so cb remains open
     *
     * @depends testOpenCB
     */
    public function testKeepOpenCB($cb)
    {
        //assert that resetTime is past since the last failure
        sleep($this->resetTime + 1);//+1 for greater than strict
        $now = time();
        $this->assertTrue($now > $cb->getStatus($this->serviceName)->getLastTry() + $cb->getConfig($this->serviceName)->getResetTime());
        $this->assertTrue($cb->isAvailable($this->serviceName));//must return true once and save lastTry
        for($i = 0; $i < $this->maxFailure+2; $i++) {
            $cb->reportFailure($this->serviceName);
        }
        Phake::verify($cb->getStorage(), Phake::times(12+13))->saveStatus(Phake::anyParameters());
        $this->assertFalse($cb->isAvailable($this->serviceName));

        $this->assertEquals($this->maxFailure, $cb->getStatus($this->serviceName)->getCount());
    }

    /**
     * after opening, waits resetTime and reports success until cb close
     *
     * @depends testOpenCB
     */
    public function testCloseCB($cb)
    {
        //assert that resetTime is past since the last failure
        sleep($this->resetTime + 1);//+1 for greater than strict
        $now = time();
        $this->assertTrue($now > $cb->getStatus($this->serviceName)->getLastTry() + $cb->getConfig($this->serviceName)->getResetTime());

        //succeed many times to close the CB (service available)
        for($i = 0; $i < $this->maxFailure+2; $i++) {
            $cb->reportSuccess($this->serviceName);
        }
        Phake::verify($cb->getStorage(), Phake::times(12+13+12))->saveStatus(Phake::anyParameters());//12 + 13 + 12 (open, keepOpen, close)
        $this->assertTrue($cb->isAvailable($this->serviceName));
        $this->assertEquals(0, $cb->getStatus($this->serviceName)->getCount());
    }



    public function dataSetLimitCount()
    {
        return [
            //[nbFailure, nbSuccess, Expected, isAvailable, nbStorageSave, Equals?]
            [0, 0, 0, true, 0, true],
            [$this->maxFailure, 0, $this->maxFailure, false, $this->maxFailure, true],
            [0, 1, -1, true, 1, false],//assert not equals -1
            [0, 10, 0, true, 10, true],//assert not equals -1
            [$this->maxFailure +1, 0, $this->maxFailure +1, false, $this->maxFailure +1, false], //assert not equals maxFailure+1
            [$this->maxFailure +1, 0, $this->maxFailure, false, $this->maxFailure+1, true],
            [2, 0, 2, true, 2, true],
            [3, 2, 1, true, 5, true],
            [2, 4, 0, true, 6, true],
            [4, 4, 0, true, 8, true],
            [20, 20, 0, true, 40, true]
        ];
    }

    /**
     * @dataProvider dataSetLimitCount
     */
    public function testCount($nbFailure, $nbSuccess, $expected, $expectedIsAvailable, $nbStorageSave, $equals)
    {
        $this->assertEquals(0, $this->circuitBreaker->getStatus($this->serviceName)->getCount());
        for($i = 0; $i < $nbFailure; $i++) {
            $this->circuitBreaker->reportFailure($this->serviceName);
        }
        for($j = 0; $j < $nbSuccess; $j++) {
            $this->circuitBreaker->reportSuccess($this->serviceName);
        }
        $isAvailable = $this->circuitBreaker->isAvailable($this->serviceName);
        $this->assertEquals($expectedIsAvailable, $isAvailable);
        Phake::verify($this->storage, Phake::times($nbStorageSave))->saveStatus(Phake::anyParameters());
        if ($equals) {
            $this->assertEquals($expected, $this->circuitBreaker->getStatus($this->serviceName)->getCount());
        } else {
            $this->assertNotEquals($expected, $this->circuitBreaker->getStatus($this->serviceName)->getCount());
        }
    }
}
