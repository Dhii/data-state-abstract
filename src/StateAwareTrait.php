<?php

namespace Dhii\Data;

use Dhii\Collection\MapInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;

/**
 * Functionality for awareness of a state data map.
 *
 * @since [*next-version*]
 */
trait StateAwareTrait
{
    /**
     * The state data map.
     *
     * @since [*next-version*]
     *
     * @var MapInterface
     */
    protected $state;

    /**
     * Retrieves the state for this instance.
     *
     * @since [*next-version*]
     *
     * @return MapInterface The map containing the state data.
     */
    protected function _getState()
    {
        return $this->state;
    }

    /**
     * Sets the state for this instance.
     *
     * @since [*next-version*]
     *
     * @param MapInterface $state A map containing the state data.
     *
     * @throws InvalidArgumentException If the state is not a valid map.
     */
    protected function _setState($state)
    {
        if ($state !== null && !($state instanceof MapInterface)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Argument is not a valid map'), null, null, $state
            );
        }

        $this->state = $state;
    }

    /**
     * Creates a new invalid argument exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param mixed|null             $argument The invalid argument, if any.
     *
     * @return InvalidArgumentException The new exception.
     */
    abstract protected function _createInvalidArgumentException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $argument = null
    );

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
