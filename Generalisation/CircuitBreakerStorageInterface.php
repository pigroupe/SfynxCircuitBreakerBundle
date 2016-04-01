<?php
/**
 * This file is part of the <CircuitBreaker> project.
 *
 * Interface CircuitBreakerStorageInterface
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
 * Interface CircuitBreakerStorageInterface
 *
 * @package    CircuitBreakerBundle
 * @subpackage   Generalisation
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 */
interface CircuitBreakerStorageInterface
{
    /**
     * @access public
     * @param $service
     * @param array $situation
     * @return mixed
     */
    public function saveStatus($service, array $situation);

    /**
     * @access public
     * @param $service
     * @return mixed
     */
    public function loadStatus($service);
}
