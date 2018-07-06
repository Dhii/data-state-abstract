<?php

namespace Dhii\Data;

use Dhii\Data\Exception\CouldNotTransitionExceptionInterface;
use Dhii\Data\Exception\TransitionerExceptionInterface;
use Dhii\State\Exception\CouldNotTransitionExceptionInterface as SmCouldNotTransitionExceptionInterface;
use Dhii\State\Exception\StateMachineExceptionInterface;
use Dhii\State\ReadableStateMachineInterface;
use Dhii\State\StateMachineInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;

/**
 * Common functionality for transitioners that transition using a state machine.
 *
 * @since [*next-version*]
 */
trait TransitionCapableStateMachineTrait
{
    /**
     * Applies a transition to a subject via a state machine.
     *
     * @since [*next-version*]
     *
     * @param StateAwareInterface    $subject    The subject to transition.
     * @param string|Stringable|null $transition The transition to apply.
     *
     * @return StateAwareInterface The transitioned subject. May or may not be the same instance as the first argument.
     */
    protected function _transition(StateAwareInterface $subject, $transition)
    {
        // Retrieve the state machine to use
        $stateMachine = $this->_getStateMachine($subject, $transition);
        // Ensure it is not null
        if ($stateMachine === null) {
            throw $this->_createTransitionerException($this->__('State machine is null'));
        }

        // Normalize the transition before using it in the state machine
        $nTransition = $this->_normalizeTransition($subject, $transition);

        try {
            // Attempt to transition using the state machine
            $rStateMachine = $stateMachine->transition($nTransition);
        } catch (SmCouldNotTransitionExceptionInterface $smtException) {
            throw $this->_createCouldNotTransitionException(
                $this->__('Failed to apply "%1$s" transition', [$transition]), null, $smtException, $subject
            );
        } catch (StateMachineExceptionInterface $smException) {
            throw $this->_createTransitionerException(
                $this->__('An error occurred during transition'), null, $smException
            );
        }

        if (!($rStateMachine instanceof ReadableStateMachineInterface)) {
            throw $this->_createTransitionerException(
                $this->__('Resulting state machine is not readable')
            );
        }

        // Determine the new subject from the input arguments and the retrieved state machine
        return $this->_getNewSubject($subject, $transition, $rStateMachine);
    }

    /**
     * Normalizes a transition before passing it on to the state machine.
     *
     * @since [*next-version*]
     *
     * @param StateAwareInterface    $subject    The subject that will be transitioned.
     * @param string|Stringable|null $transition The transition to normalize.
     *
     * @return string|Stringable|null The normalized transition.
     */
    abstract protected function _normalizeTransition(StateAwareInterface $subject, $transition);

    /**
     * Retrieves the state machine associated with this instance.
     *
     * @since [*next-version*]
     *
     * @param StateAwareInterface    $subject    The subject that will be transitioned.
     * @param string|Stringable|null $transition The transition.
     *
     * @return StateMachineInterface|null The state machine.
     */
    abstract protected function _getStateMachine(StateAwareInterface $subject, $transition);

    /**
     * Retrieves the new subject instance.
     *
     * @since [*next-version*]
     *
     * @param StateAwareInterface           $subject      The subject that was transitioned.
     * @param string|Stringable|null        $transition   The transition that was applied.
     * @param ReadableStateMachineInterface $stateMachine The state machine that resulted from transitioning.
     *
     * @return StateAwareInterface The new subject instance.
     */
    abstract protected function _getNewSubject(
        StateAwareInterface $subject,
        $transition,
        ReadableStateMachineInterface $stateMachine
    );

    /**
     * Creates a transitioner exception instance.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null     $message      The error message, if any.
     * @param int|null                   $code         The error code, if any.
     * @param RootException|null         $previous     The previous exception for chaining, if any.
     * @param TransitionerInterface|null $transitioner The transitioner, if any.
     *
     * @return TransitionerExceptionInterface The created exception instance.
     */
    abstract protected function _createTransitionerException(
        $message = null,
        $code = null,
        RootException $previous = null,
        TransitionerInterface $transitioner = null
    );

    /**
     * Creates an exception instance for failed transitions.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null   $message    The error message, if any.
     * @param int|null                 $code       The error code, if any.
     * @param RootException|null       $previous   The previous exception for chaining, if any.
     * @param StateAwareInterface|null $subject    The subject, if any.
     * @param string|Stringable|null   $transition The transition, if any.
     *
     * @return CouldNotTransitionExceptionInterface The created exception instance.
     */
    abstract protected function _createCouldNotTransitionException(
        $message = null,
        $code = null,
        RootException $previous = null,
        StateAwareInterface $subject = null,
        $transition = null
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
