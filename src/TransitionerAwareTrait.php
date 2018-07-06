<?php

namespace Dhii\Data;

use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;

/**
 * Common functionality for objects that are aware of a booking transitioner.
 *
 * @since [*next-version*]
 */
trait TransitionerAwareTrait
{
    /**
     * The booking transitioner associated with this instance.
     *
     * @since [*next-version*]
     *
     * @var TransitionerInterface|null
     */
    protected $transitioner;

    /**
     * Retrieves the booking transitioner associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return TransitionerInterface|null The transitioner, if any.
     */
    protected function _getTransitioner()
    {
        return $this->transitioner;
    }

    /**
     * Sets the booking transitioner for this instance.
     *
     * @since [*next-version*]
     *
     * @param TransitionerInterface|null $transitioner The transitioner or null.
     *
     * @throws InvalidArgumentException If the argument is not valid.
     */
    protected function _setTransitioner($transitioner)
    {
        if ($transitioner !== null && !($transitioner instanceof TransitionerInterface)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Argument is not a valid booking transitioner'),
                null,
                null,
                $transitioner
            );
        }

        $this->transitioner = $transitioner;
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
