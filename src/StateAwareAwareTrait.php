<?php

namespace Dhii\Data;

use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;

/**
 * Provides awareness for state-aware objects.
 *
 * @since [*next-version*]
 */
trait StateAwareAwareTrait
{
    /**
     * The state-aware subject.
     *
     * @since [*next-version*]
     *
     * @var StateAwareInterface
     */
    protected $stateAware;

    /**
     * Retrieves the state-aware subject associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return StateAwareInterface The state-aware subject instance.
     */
    protected function _getStateAware()
    {
        return $this->stateAware;
    }

    /**
     * Sets the state-aware subject for with this instance.
     *
     * @since [*next-version*]
     *
     * @param StateAwareInterface $stateAware The state-aware subject instance.
     */
    protected function _setStateAware($stateAware)
    {
        if ($stateAware !== null && !($stateAware instanceof StateAwareInterface)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Argument is not a state aware object'),
                null,
                null,
                $stateAware
            );
        }

        $this->stateAware = $stateAware;
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
