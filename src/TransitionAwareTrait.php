<?php

namespace Dhii\Data;

use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;

/**
 * Provides awareness for a transition.
 *
 * @since [*next-version*]
 */
trait TransitionAwareTrait
{
    /**
     * The transition.
     *
     * @since [*next-version*]
     *
     * @var string|Stringable|null
     */
    protected $transition;

    /**
     * Retrieves the transition associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return string|Stringable|null The transition.
     */
    protected function _getTransition()
    {
        return $this->transition;
    }

    /**
     * Sets the transition for with this instance.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $transition The transition.
     */
    protected function _setTransition($transition)
    {
        $this->transition = $this->_normalizeStringable($transition);
    }

    /**
     * Normalizes a stringable value.
     *
     * Useful to make sure that a value can be converted to string in a meaningful way.
     *
     * @since [*next-version*]
     *
     * @param Stringable|string|int|float|bool $stringable The value to normalize.
     *                                                     Can be an object that implements {@see Stringable}, or
     *                                                     scalar type - basically anything that can be converted to a
     *                                                     string in a meaningful way.
     *
     * @throws InvalidArgumentException If the value could not be normalized.
     *
     * @return Stringable|string|int|float|bool The normalized stringable.
     *                                          If the original value was stringable, that same value will be returned
     *                                          without any modification.
     */
    abstract protected function _normalizeStringable($stringable);
}
