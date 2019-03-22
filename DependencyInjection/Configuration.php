<?php
/**
 * This file is part of the <CircuitBreaker> project.
 *
 * @package    CircuitBreakerBundle
 * @subpackage   DependencyInjection
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\CircuitBreakerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This class contains the configuration information for the bundle
 *
 * @package    CircuitBreakerBundle
 * @subpackage   DependencyInjection
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sfynx_circuit_breaker');

        $this->addCacheDir($rootNode);
        $this->addServiceConfig($rootNode);
        $this->addCacheClientProviderConfig($rootNode);

        return $treeBuilder;
    }

    /**
     * cache directory where service states are stored
     * the value must finish with "/"
     *
     * @access private
     * @param ArrayNodeDefinition $rootNode
     * @return void
     */
    private function addCacheDir(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('cache_dir')->isRequired()
            ->end()
        ;

        return $rootNode;
    }

    /**
     * service configuration definitions
     *
     * @access private
     * @param ArrayNodeDefinition $rootNode
     * @return void
     */
    private function addServiceConfig(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('service_names')
                    ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('max_failure')->defaultValue(20)->end()
                                ->scalarNode('reset_time')->defaultValue(30)->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $rootNode;
    }

    /**
     * Cache client provider used with cache provider
     *
     * @access private
     * @param ArrayNodeDefinition $rootNode
     * @return void
     */
    private function addCacheClientProviderConfig(ArrayNodeDefinition $rootNode)
    {
        $rootNode
          ->children()
            ->scalarNode('cache_client_provider')->defaultValue('sfynx.cache.filecache')->end()
          ->end()
        ;

        return $rootNode;
    }
}
