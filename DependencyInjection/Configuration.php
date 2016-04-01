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

        return $treeBuilder;
    }

    /**
     * cache directory where service states are stored
     * the value must finish with "/"
     *
     * @access private
     * @param ArrayNodeDefinition $node
     * @return void
     */
    private function addCacheDir(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->scalarNode('cache_dir')->isRequired()
            ->end()
        ;
    }

    /**
     * service configuration definitions
     *
     * @access private
     * @param ArrayNodeDefinition $node
     * @return void
     */
    private function addServiceConfig(ArrayNodeDefinition $node)
    {
        $node
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
    }
}
