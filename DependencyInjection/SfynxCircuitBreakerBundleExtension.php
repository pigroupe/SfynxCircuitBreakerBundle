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

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * @package    CircuitBreakerBundle
 * @subpackage   DependencyInjection
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 */
class SfynxCircuitBreakerBundleExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        //load config for service names
        $definition = $container->getDefinition('sfynx.circuitbreaker');
        $definition->addMethodCall('loadConfig', [$config['service_names']]);

        //load config for cache_dir
        $definition = $container->getDefinition('sfynx.circuitbreaker.storage');
        $definition->addMethodCall('loadConfig', [$config['cache_dir']]);

        //Optionnal overriding mechanism on default cache client (filecache) through application configuration
        if(
          !is_null($config['cache_client_provider'])
          && !empty($config['cache_client_provider']))
        {
            // replaces first argument which stands for cache client provider
            $definition->replaceArgument(0, new Reference($config['cache_client_provider']));
        }
    }
    /**
     * @return string
     */
    public function getAlias()
    {
        return 'sfynx_circuit_breaker';
    }
}
