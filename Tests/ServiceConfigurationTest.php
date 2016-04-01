<?php

namespace Sfynx\CircuitBreakerBundle\Tests;

use Sfynx\CircuitBreakerBundle\CircuitBreaker\ServiceConfiguration;

class ServiceConfigurationTest extends \PHPUnit_Framework_TestCase
{
    protected $serviceConfiguration;
    protected $maxFailure;
    protected $resetTime;

    public function setUp()
    {
        $this->maxFailure = 10;
        $this->resetTime = 50;
        $this->serviceConfiguration = new ServiceConfiguration($this->maxFailure, $this->resetTime);
    }

    public function testGetters()
    {
        $this->assertEquals($this->maxFailure, $this->serviceConfiguration->getMaxFailure());
        $this->assertEquals($this->resetTime, $this->serviceConfiguration->getResetTime());
    }

    public function testSetters()
    {
        $this->serviceConfiguration->setMaxFailure(12);
        $this->serviceConfiguration->setResetTime(20);
        $this->assertEquals(12, $this->serviceConfiguration->getMaxFailure());
        $this->assertEquals(20, $this->serviceConfiguration->getResetTime());
    }

    /**
     * @expectedException     \Exception
     */
    public function testMaxFailureSetterWithBoolean()
    {
        $this->serviceConfiguration->setMaxFailure(true);
    }

    /**
     * @expectedException     \Exception
     */
    public function testResetTimeSetterWithBoolean()
    {
        $this->serviceConfiguration->setResetTime(true);
    }

    /**
     * @expectedException     \Exception
     */
    public function testMaxFailureSetterWitString()
    {
        $this->serviceConfiguration->setMaxFailure("a string not an integer");
    }

    /**
     * @expectedException     \Exception
     */
    public function testResetTimeSetterWithStringNumber()
    {
        $this->serviceConfiguration->setResetTime("50");
    }

    /**
     * @expectedException     \Exception
     */
    public function testMaxFailureSetterWitStringNumber()
    {
        $this->serviceConfiguration->setMaxFailure("50");
    }

    /**
     * @expectedException     \Exception
     */
    public function testResetTimeSetterWithString()
    {
        $this->serviceConfiguration->setResetTime("a string not an integer");
    }

    /**
     * @expectedException     \Exception
     */
    public function testMaxFailureSetterWithNegativeValue()
    {
        $this->serviceConfiguration->setMaxFailure(-30);
    }

    /**
     * @expectedException     \Exception
     */
    public function testMaxFailureSetterWithNullValue()
    {
        $this->serviceConfiguration->setMaxFailure(null);
    }

    /**
     * @expectedException     \Exception
     */
    public function testMaxFailureSetterWithZeroValue()
    {
        $this->serviceConfiguration->setMaxFailure(0);
    }

    /**
     * @expectedException     \Exception
     */
    public function testResetTimeSetterWithNegativeValue()
    {
        $this->serviceConfiguration->setResetTime(-30);
    }

    /**
     * @expectedException     \Exception
     */
    public function testResetTimeSetterWithNullValue()
    {
        $this->serviceConfiguration->setResetTime(null);
    }

    /**
     * @expectedException     \Exception
     */
    public function testResetTimeSetterWithZeroValue()
    {
        $this->serviceConfiguration->setResetTime(0);
    }
}
