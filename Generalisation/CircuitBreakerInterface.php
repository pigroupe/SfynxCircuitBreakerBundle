<?php
/**
 * This file is part of the <CircuitBreaker> project.
 *
 * Interface CircuitBreakerInterface
 *
 * @package    CircuitBreakerBundle
 * @subpackage   Generalisation
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\CircuitBreakerBundle\Generalisation;

/**
 * Interface CircuitBreakerInterface
 *
 * @package    CircuitBreakerBundle
 * @subpackage   Generalisation
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 */
interface CircuitBreakerInterface
{
    /**
     * Check if the service is available
     *
     * @access public
     * @param string $serviceName
     * @return boolean true if service is available, false if service is down
     */
    public function isAvailable($serviceName);

    /**
     * This method must be called if the http call fails
     *
     * It decrements the number of error and update the last try time for a given service
     * and then save the status with the storage
     *
     * @access public
     * @param string $serviceName
     * @return void
     */
    public function reportFailure($serviceName);

    /**
     * This method must be called if the http call succeeds
     *
     * It increments the number of error and update the last try time for a given service
     * and then save the status with the storage
     *
     * @access public
     * @param string $serviceName
     * @return void
     */
    public function reportSuccess($serviceName);
}
