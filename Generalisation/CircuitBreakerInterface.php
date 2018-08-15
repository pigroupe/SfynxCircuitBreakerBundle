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

use Sfynx\CircuitBreakerBundle\Exception\UnavailableServiceException;
use Sfynx\CircuitBreakerBundle\CircuitBreaker\ServiceConfiguration;

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
    public function isAvailable(string $serviceName);

    /**
     * @param string $serviceName
     * @throws UnavailableServiceException
     */
    public function checkAvailable(string $serviceName);

    /**
     * This method allow to register service in code
     *
     * @access public
     * @param string $serviceName
     * @param ServiceConfiguration $configuration
     * @return void
     * @throws \Exception
     */
    public function registerService(string $serviceName, ServiceConfiguration $configuration);

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
    public function reportFailure(string $serviceName);

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
    public function reportSuccess(string $serviceName);
}
