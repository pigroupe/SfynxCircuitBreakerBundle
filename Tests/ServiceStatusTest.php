<?php

namespace Sfynx\CircuitBreakerBundle\Tests;

use Sfynx\CircuitBreakerBundle\CircuitBreaker\ServiceStatus;

class ServiceStatusTest extends \PHPUnit_Framework_TestCase
{
    protected $serviceStatus;
    protected $count = 10;
    protected $lastTry;
    protected $maxFailure = 20;

    public function setUp()
    {
        $this->lastTry = time();
        $this->serviceStatus = new ServiceStatus($this->lastTry, $this->count);
    }

    public function testDefaultValues()
    {
        $serviceStatus = new ServiceStatus();
        $this->assertEquals(0, $serviceStatus->getCount());
        $this->assertEquals(time(), $serviceStatus->getLastTry());

        return $this->serviceStatus;
    }

    public function testGetters()
    {
        $this->assertEquals($this->lastTry, $this->serviceStatus->getLastTry());
        $this->assertEquals($this->count, $this->serviceStatus->getCount());
    }

    public function testSetters()
    {
        $this->serviceStatus->setLastTry(12);
        $this->assertEquals(12, $this->serviceStatus->getLastTry());
    }

    public function dataSetLimitCount()
    {
        return [
            //[nbAddCount, nbSubCount, Expected, Equals?]
            [0, 0, 0, true],
            [$this->count, 0, $this->count, true],
            [0, 1, -1, false],//assert not equals -1
            [$this->maxFailure +1, 0, $this->maxFailure +1, false], //assert not equals maxFailure+1
            [$this->maxFailure +10, 0, $this->maxFailure, true],
            [2, 0, 2, true],
            [0, 5, 0, true],
            [3, 2, 1, true],
            [2, 4, 0, true],
            [4, 4, 0, true],
        ];
    }

    /**
     * @dataProvider dataSetLimitCount
     */
    public function testCount($nbAddCount, $nbSubCount, $expected, $equals)
    {
        $this->serviceStatus = new ServiceStatus();
        $this->assertEquals(0, $this->serviceStatus->getCount());
        for($i = 0; $i < $nbAddCount; $i++) {
            $this->serviceStatus->addCount($this->maxFailure);
        }
        for($j = 0; $j < $nbSubCount; $j++) {
            $this->serviceStatus->subCount();
        }

        if ($equals) {
            $this->assertEquals($expected, $this->serviceStatus->getCount());
        } else {
            $this->assertNotEquals($expected, $this->serviceStatus->getCount());
        }
    }

    public function testToArray()
    {
        $array = $this->serviceStatus->toArray();
        $this->assertTrue(is_array($array));
        $this->assertEquals(2, count($array));
        $this->assertTrue(array_key_exists('lastTry', $array));
        $this->assertTrue(array_key_exists('count', $array));
        $this->assertEquals($this->lastTry, $array['lastTry']);
        $this->assertEquals($this->count, $array['count']);
    }

    /**
     * @expectedException     \Exception
     */
    public function testLastTrySetterWithBoolean()
    {
        $this->serviceStatus->setLastTry(true);
    }

    /**
     * @expectedException     \Exception
     */
    public function testLastTrySetterWitString()
    {
        $this->serviceStatus->setLastTry("a string not an integer");
    }

    /**
     * @expectedException     \Exception
     */
    public function testLastTrySetterWitStringNumber()
    {
        $this->serviceStatus->setLastTry("50");
    }

    /**
     * @expectedException     \Exception
     */
    public function testLastTrySetterWithNegativeValue()
    {
        $this->serviceStatus->setLastTry(-30);
    }

    /**
     * @expectedException     \Exception
     */
    public function testCountInConstructorWithBoolean()
    {
        $this->serviceStatus = new ServiceStatus(time(), true);
    }

    /**
     * @expectedException     \Exception
     */
    public function testCountInConstructorWitString()
    {
        $this->serviceStatus = new ServiceStatus(time(), "a string not an integer");
    }

    /**
     * @expectedException     \Exception
     */
    public function testCountInConstructorWitStringNumber()
    {
        $this->serviceStatus = new ServiceStatus(time(), "50");
    }

    /**
     * @expectedException     \Exception
     */
    public function testCountInConstructorWithNegativeValue()
    {
        $this->serviceStatus = new ServiceStatus(time(), -50);
    }
}
