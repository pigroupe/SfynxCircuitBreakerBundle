<?php
/**
 * This file is part of the <CircuitBreaker> project.
 *
 * @package    CircuitBreakerBundle
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\CircuitBreakerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Sfynx\CircuitBreakerBundle\DependencyInjection\SfynxCircuitBreakerBundleExtension;

/**
 * Sfynx configuration and managment of the circuit breaker Bundle
 *
 * class SfynxCircuitBreakerBundle
 *
 * @package    CircuitBreakerBundle
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 */
class SfynxCircuitBreakerBundle extends Bundle
{
    /**
     * @access public
     * @return SfynxCircuitBreakerBundleExtension
     */
    public function getContainerExtension()
    {
        return new SfynxCircuitBreakerBundleExtension();
    }

}
