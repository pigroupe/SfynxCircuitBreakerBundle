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
 * class ServiceConfiguration
 *
 * @package    CircuitBreakerBundle
 * @subpackage   CircuitBreaker
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 */
class ServiceConfiguration
{
    use TraitCheckValues;

    /**
     * @access protected
     * @var int $maxFailure The number of accepted failure before stopping the service (>0)
     */
    protected $maxFailure;

    /**
     * @access protected
     * @var int $resetTime Time to wait in second to be allowed to retry (>0)
     */
    protected $resetTime;

    /**
     * ServiceConfiguration constructor.
     *
     * @access public
     * @param $maxFailure
     * @param $resetTime
     */
    public function __construct($maxFailure, $resetTime)
    {
        $this->setMaxFailure($maxFailure);
        $this->setResetTime($resetTime);
    }

    /**
     * Getter of maxFailure
     *
     * @access public
     * @return int
     */
    public function getMaxFailure()
    {
        return $this->maxFailure;
    }

    /**
     * Setter of maxFailure
     *
     * @access public
     * @param mixed $maxFailure
     * @return void
     */
    public function setMaxFailure($maxFailure)
    {
        $this->checkPositifInteger($maxFailure);
        $this->maxFailure = $maxFailure;
    }

    /**
     * Getter of resetTime
     *
     * @access public
     * @return int
     */
    public function getResetTime()
    {
        return $this->resetTime;
    }

    /**
     * Setter of resetTime
     *
     * @access public
     * @param int $resetTime
     * @return void
     */
    public function setResetTime($resetTime)
    {
        $this->checkPositifInteger($resetTime);
        $this->resetTime = $resetTime;
    }
}
