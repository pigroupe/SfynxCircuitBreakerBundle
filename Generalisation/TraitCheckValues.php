<?php

namespace Sfynx\CircuitBreakerBundle\Generalisation;


trait TraitCheckValues
{
    /**
     * Verify if $value is an integer > 0
     *
     * @access protected
     * @param mixed $value
     * @param boolean $canBeNull If $canBeNull is true and value is null : do nothing
     * @throws \Exception
     */
    protected function checkPositifInteger($value, $canBeNull = false)
    {
        if ($canBeNull && null === $value) {
            return;
        }
        if (!is_int($value) || $value <= 0) {
            throw new \Exception('Invalid argument. It must be a positif integer');
        }
    }

    /**
     * Verify if $value is an integer >= 0
     *
     * @access protected
     * @param mixed $value
     * @param boolean $canBeNull If $canBeNull is true and value is null : do nothing
     * @throws \Exception
     */
    protected function checkPositifOrZeroInteger($value, $canBeNull = false)
    {
        if ($canBeNull && null === $value) {
            return;
        }
        if (!is_int($value) || $value < 0) {
            throw new \Exception('Invalid argument. It must be zero or a positif integer');
        }
    }
}
