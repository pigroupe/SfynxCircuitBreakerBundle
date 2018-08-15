# SfynxCircuitBreakerBundle documentation

Using Http retries carelessly could result in creating a Denial of Service (DoS) attack within your own software.
As a microservice fails or performs slowly, multiple clients might repeatedly retry failed requests.
That creates a dangerous risk of exponentially increasing traffic targeted at the failing service.

Therefore, you need some kind of defense barrier so that excessive requests stop when it is not worth keeping trying.
That defense barrier is precisely the circuit breaker.

The Circuit Breaker pattern has a different purpose than the "Retry pattern". The "Retry pattern" enables an application
to retry an operation in the expectation that the operation will eventually succeed.
The Circuit Breaker pattern prevents an application from performing an operation that is likely to fail.
An application can combine these two patterns. However, the retry logic should be sensitive to any exception returned
by the circuit breaker, and it should abandon retry attempts if the circuit breaker indicates that a fault is not transient.

The following documents are available:

- [Configuration reference](configuration_reference.md)
- [ChangeLog](#changelog)
- [Todo](#todo)

## Implementation of your own circuit breaker pattern

The goal of this bundle is to provide a service to your system in order to implement circuit breaker pattern.

###### a) First step, define your own circuit breaker

Here we define a cb named `myCbName` with specific parameters.

```yaml
#
# SfynxCircuitBreakerBundle configuration
#
sfynx_circuit_breaker:
    cache_dir: "/tmp/"  # must finish with "/"
    service_names:
        myCbName:
            max_failure: 5
            reset_time: 50
```

**max_failure**: value that defines the number of errors accepted before triggering the circuit breaker.

**reset_time**: value that defines the number of execution that must be performed in order to retest the execution of the process.

###### a) Second step, implement pattern in your code

```php
<?php
namespace Sfynx\Cb;

use Sfynx\CircuitBreakerBundle\Exception\UnavailableServiceException;
use Sfynx\CircuitBreakerBundle\Generalisation\CircuitBreakerInterface;

class MyClass
{
    /** @var CircuitBreakerInterface */
    protected $circuitBreaker;
    /** @var string */
    protected $circuitBreakerName;

    /**
     * Constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->circuitBreaker = $this->get('sfynx.circuitbreaker');
        $this->circuitBreakerName = 'myCbName';
    }

    /**
     * Execute process.
     *
     * @throws UnavailableServiceException
     */
    public function process()
    {
        $this->circuitBreaker->checkAvailable($this->circuitBreakerName);

        try {
            // execute code that needs to be tested.
            ...

            $this->circuitBreaker->reportSuccess($this->circuitBreakerName);
        } catch (Exception $exception) {
            $this->circuitBreaker->reportFailure($this->circuitBreakerName);
            throw UnavailableServiceException::serviceCallFailure($this->circuitBreakerName);
        }
    }
```


**NB**: `checkAvailable` method returns an `UnavailableServiceException` exception as long as the `process` method has
has not been called the number of times equal to the `reset_time` value.

## ChangeLog

| Date | Version | Auteur | Description |
| ------ | ----------- | ---- | ----------- |
| 20/07/2018   | 1.0.0 | EDL | documentation initialization|

## Todo
