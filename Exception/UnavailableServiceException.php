<?php
/**
 * This file is part of the <CircuitBreaker> project.
 *
 * @package    CircuitBreakerBundle
 * @subpackage   Exception
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\CircuitBreakerBundle\Exception;

use Exception;

/**
 * class UnavailableServiceException
 *
 * @package    CircuitBreakerBundle
 * @subpackage   Exception
 * @author Laurent DE NIL <laurent.denil@gmail.com>
 */
class UnavailableServiceException extends Exception
{
    /**
     * @param string $message
     * @param int $errorNum
     * @param Exception|null $previous
     */
    public function __construct($message = '', $errorNum = 500, Exception $previous = null)
    {
        parent::__construct($message, $errorNum, $previous);
    }

    /**
     * Returns the <unavailable service> Exception.
     *
     * @param string $serviceName
     * @return UnavailableServiceException
     */
    public static function unvailableService($serviceName)
    {
        return new static(sprintf('The service %s is unavailable', $serviceName), 503);
    }

    /**
     * Returns the <service call failure> Exception.
     *
     * @param string $serviceName
     * @return UnavailableServiceException
     */
    public static function serviceCallFailure($serviceName)
    {
        return new static(sprintf('The call for service %s has failed', $serviceName), 503);
    }
}
