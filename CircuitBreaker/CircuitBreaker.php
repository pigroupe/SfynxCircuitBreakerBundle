<?php
/**
 * This file is part of the <CircuitBreaker> project.
 *
 * @uses CircuitBreakerInterface
 * @package    CircuitBreakerBundle
 * @subpackage   CircuitBreaker
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\CircuitBreakerBundle\CircuitBreaker;

use Sfynx\CircuitBreakerBundle\Generalisation\CircuitBreakerStorageInterface;
use Sfynx\CircuitBreakerBundle\Generalisation\CircuitBreakerInterface;
use Sfynx\CircuitBreakerBundle\Exception\UnavailableServiceException;

/**
 * class CircuitBreaker, an implementation of the circuit breaker pattern
 *
 * @todo : show an example how to use
 *
 * @uses CircuitBreakerInterface
 * @package    CircuitBreakerBundle
 * @subpackage   CircuitBreaker
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 */
class CircuitBreaker implements CircuitBreakerInterface
{
    /**
     * @access protected
     * @var array $serviceStatusArray Array of ServiceState Objects
     */
    protected $serviceStatusArray = [];
    /**
     * @access protected
     * @var array $serviceConfigArray Array of ServiceConfiguration Objects
     */
    protected $serviceConfigArray;

    /**
     * @access protected
     * @var CircuitBreakerStorageInterface $storage object which save service status
     */
    protected $storage;

    /**
     * CircuitBreaker constructor
     *
     * @access public
     * @param CircuitBreakerStorageInterface $storage
     */
    public function __construct(CircuitBreakerStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * This method is called in the bundle extension to load configuration from config.yml
     *
     * @access public
     * @param array $serviceConfigArray Array from config.yml with key : sfynx_circuit_breaker.services
     * @return void
     */
    public function loadConfig(array $serviceConfigArray)
    {
        foreach ($serviceConfigArray as $serviceName => $config) {
            $this->setConfig($serviceName, new ServiceConfiguration($config['max_failure'], $config['reset_time']));
            $status = $this->storage->loadStatus($serviceName);//load status from sfynx cache if exists
            if ($status) {
                $this->setStatus($serviceName, new ServiceStatus($status['lastTry'], $status['count']));
            } else {
                $this->setStatus($serviceName, new ServiceStatus());
                //save status in cache
                $this->storage->saveStatus($serviceName, $this->getStatus($serviceName)->toArray());
            }
        }
    }

    /**
     * This method allow to register service in code
     *
     * @access public
     * @param string $serviceName
     * @param ServiceConfiguration $configuration
     * @return void
     * @throws \Exception
     */
    public function registerService($serviceName, ServiceConfiguration $configuration)
    {
        if (!empty($this->serviceConfigArray[$serviceName])) {
            throw new \Exception('service already exists');
        }
        $this->setStatus($serviceName, new ServiceStatus());
        $this->setConfig($serviceName, $configuration);
    }

    /**
     * Return the ServiceStatus object of a given service
     *
     * @access public
     * @param string $serviceName
     * @return ServiceStatus
     * @throws \Exception
     */
    public function getStatus($serviceName)
    {
        $this->checkServiceName($serviceName);

        return $this->serviceStatusArray[$serviceName];
    }

    /**
     * Return the ServiceConfiguration object of a given service
     *
     * @access public
     * @param string $serviceName
     * @return ServiceConfiguration
     * @throws \Exception
     */
    public function getConfig($serviceName)
    {
        $this->checkServiceName($serviceName);

        return $this->serviceConfigArray[$serviceName];
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable($serviceName)
    {
        //first, load from cache
        $statusFromCache = $this->storage->loadStatus($serviceName);
        $status = $this->getStatus($serviceName);
        if ($statusFromCache) {
            $status->setLastTry($statusFromCache['lastTry']);
            $status->setCount($statusFromCache['count']);
        }

        if ($status->getCount() < $this->getConfig($serviceName)->getMaxFailure()) {
            return true;
        }

        //here we are at maxFailure
        if ($status->getLastTry() + $this->getConfig($serviceName)->getResetTime() < time()) {//lastTry + resetTime is past
            $status->setLastTry(time());//we update time to now
            $this->storage->saveStatus($serviceName, $status->toArray());//save in cache
            return true;
        }

        //lastTry + resetTime is not past
        return false;
    }

    /**
     * @param $serviceName
     * @throws UnavailableServiceException
     */
    public function checkAvailable($serviceName)
    {
        if (!$this->isAvailable($serviceName)) {
            throw UnavailableServiceException::unvailableService($serviceName);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reportFailure($serviceName)
    {
        $this->checkServiceName($serviceName);
        $max = $this->getConfig($serviceName)->getMaxFailure();
        $this->storage->saveStatus($serviceName, $this->getStatus($serviceName)->addCount($max)->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function reportSuccess($serviceName)
    {
        $this->checkServiceName($serviceName);
        $this->storage->saveStatus($serviceName, $this->getStatus($serviceName)->subCount()->toArray());
    }

    /**
     * Getter of $storage
     *
     * @return CircuitBreakerStorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Setter of status array for a given service name
     *
     * @access protected
     * @param $serviceName
     * @param ServiceStatus $status
     * @return void
     */
    protected function setStatus($serviceName, ServiceStatus $status)
    {
        $this->serviceStatusArray[$serviceName] = $status;
    }

    /**
     * Setter of config array for a given service name
     *
     * @access protected
     * @param $serviceName
     * @param ServiceConfiguration $config
     * @return void
     */
    protected function setConfig($serviceName, ServiceConfiguration $config)
    {
        $this->serviceConfigArray[$serviceName] = $config;
    }

    /**
     * Check if the service name is registered in circuit breaker. Otherwise throw an exception
     *
     * @access protected
     * @param $serviceName
     * @throws \Exception
     * @return void
     */
    protected function checkServiceName($serviceName)
    {
        if (!array_key_exists($serviceName, $this->serviceConfigArray) || !array_key_exists($serviceName, $this->serviceStatusArray)) {
            throw new \Exception('The service name doesn\'t exist');
        }
    }
}
