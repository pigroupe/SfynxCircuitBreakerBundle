<?php
/**
 * This file is part of the <CircuitBreaker> project.
 *
 * @uses CircuitBreakerStorageInterface
 * @package    CircuitBreakerBundle
 * @subpackage   Storage
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\CircuitBreakerBundle\Storage;

use Sfynx\CircuitBreakerBundle\Generalisation\CircuitBreakerStorageInterface;
use Sfynx\CacheBundle\Manager\Generalisation\CacheInterface;

/**
 * class JsonStorage
 *
 * @uses CircuitBreakerStorageInterface
 * @package    CircuitBreakerBundle
 * @subpackage   CircuitBreaker
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 *
 * @TODO add TTL in config.yml
 */
class JsonStorage implements CircuitBreakerStorageInterface
{
    /**
     * @access protected
     * @var string $cacheDir It must finishes with "/"
     */
    protected $cacheDir;

    /**
     * @access protected
     * @var CacheInterface $factory
     */
    protected $factory;

    /**
     * @access protected
     * @var CacheClientInterface $client
     */
    protected $client;

    /**
     * JsonStorage constructor.
     *
     * @access public
     * @param CacheInterface $cacheFactory
     */
    public function __construct(CacheInterface $cacheFactory)
    {
        $this->factory = $cacheFactory;
    }

    /**
     * Save status for a given service name
     *
     * @access public
     * @param $serviceName
     * @param array $status
     * @return void
     */
    public function saveStatus($serviceName, array $status)
    {
        $this->client->set($serviceName, $status);// with default ttl = 300
    }

    /**
     * Load status from cache file for a given service name
     *
     * @access public
     * @param $serviceName
     * @return array
     */
    public function loadStatus($serviceName)
    {
        return $this->client->get($serviceName);
    }

    /**
     * load config from config.yml. It is used in dependencyInjection
     *
     * @access public
     * @param string $cacheDir Directory where cache files are stored
     * @return void
     * @throws \Exception
     */
    public function loadConfig(string $cacheDir)
    {
        $this->cacheDir = $cacheDir;
        $this->client = $this->factory->getClient();
        if(!$this->client->setPath($this->cacheDir)) {
            throw new \Exception('SfynxCache, invalid cache directory');
        }
    }
}
