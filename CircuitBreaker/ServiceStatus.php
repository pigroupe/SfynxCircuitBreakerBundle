<?php
/**
 * This file is part of the <CircuitBreaker> project.
 *
 * @package    CircuitBreakerBundle
 * @subpackage   CircuitBreaker
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\CircuitBreakerBundle\CircuitBreaker;

use Sfynx\CircuitBreakerBundle\Generalisation\TraitCheckValues;

/**
 * class ServiceStatus
 *
 * @package    CircuitBreakerBundle
 * @subpackage   CircuitBreaker
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 */
class ServiceStatus
{
    use TraitCheckValues;

    /**
     * @access protected
     * @var int $lastTry The timestamp of the last try
     */
    protected $lastTry;

    /**
     * @access protected
     * @var int $count The number of failure
     */
    protected $count;

    /**
     * ServiceStatus constructor.
     *
     * @access public
     * @param null $lastTry
     * @param int $count
     */
    public function __construct($lastTry = null, $count = 0)
    {
        $this->setLastTry($lastTry);
        $this->setCount($count);
    }

    /**
     * Getter of lastTry
     *
     * @access public
     * @return int (timestamp)
     */
    public function getLastTry()
    {
        return $this->lastTry;
    }

    /**
     * Getter of count
     *
     * @access public
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * increments count value and set lastTry to current timestamp
     *
     * @access public
     * @param integer $maxFailure from ServiceConfiguration
     * @return $this
     */
    public function addCount($maxFailure)
    {
        $this->setLastTry(time());
        if ($this->getCount() < $maxFailure) {
            $this->setCount($this->getCount() + 1);
        }

        return $this;
    }

    /**
     * decrements count value and set lastTry to current timestamp
     *
     * @access public
     * @return $this
     */
    public function subCount()
    {
        $this->setLastTry(time());
        if ($this->getCount() > 0) {
            $this->setCount($this->getCount() - 1);
        }

        return $this;
    }

    /**
     * Return an array like this format :
     *
     * ['lastTry' => (a_timestamp), 'count' => (an integer)]
     *
     * @access public
     * @return array
     */
    public function toArray()
    {
        return ['lastTry' => $this->getLastTry(), 'count' => $this->getCount()];
    }

    /**
     * Setter of lastTry
     *
     * @access public
     * @param $time
     * @return $this
     */
    public function setLastTry($time)
    {
        $this->checkPositifOrZeroInteger($time, true);
        $this->lastTry = $time;
        if (null === $this->lastTry) {
            $this->lastTry = time();
        }

        return $this;
    }

    /**
     * Setter of count
     *
     * @access protected
     * @param $count
     */
    public function setCount($count)
    {
        $this->checkPositifOrZeroInteger($count, true);
        $this->count = $count;
    }
}
