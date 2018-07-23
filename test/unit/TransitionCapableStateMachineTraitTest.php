<?php

namespace Dhii\Data\UnitTest;

use Dhii\Data\StateAwareInterface;
use Exception;
use PHPUnit_Framework_MockObject_MockBuilder;
use PHPUnit_Framework_MockObject_MockObject;
use Xpmock\TestCase;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class TransitionCapableStateMachineTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Data\TransitionCapableStateMachineTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods The methods to mock, as an array of method names.
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function createInstance($methods = [])
    {
        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                     ->setMethods(
                         array_merge(
                             [
                                 '_normalizeTransition',
                                 '_getStateMachineFor',
                                 '_getNewSubject',
                                 '_createTransitionerException',
                                 '_createCouldNotTransitionException',
                                 '__',
                             ],
                             $methods
                         )
                     )
                     ->getMockForTrait();

        $mock->method('__')->willReturnArgument(0);
        $mock->method('_createTransitionerException')->willReturnCallback(
            function ($message = '', $code = 0, $prev = null) {
                return new Exception($message, $code, $prev);
            }
        );
        $mock->method('_createCouldNotTransitionException')->willReturnCallback(
            function ($message = '', $code = 0, $prev = null) {
                return new Exception($message, $code, $prev);
            }
        );

        return $mock;
    }

    /**
     * Creates a mock state-aware instance for testing purposes.
     *
     * @since [*next-version*]
     *
     * @return StateAwareInterface
     */
    public function createStateAware()
    {
        $mock = $this->mock('Dhii\Data\StateAwareInterface')
                     ->getState();

        return $mock->new();
    }

    /**
     * Creates a mock state machine instance for testing purposes.
     *
     * @since [*next-version*]
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function createStateMachine()
    {
        $mock = $this->getMockBuilder('Dhii\State\StateMachineInterface')
                     ->setMethods(['transition', 'canTransition'])
                     ->getMockForAbstractClass();

        return $mock;
    }

    /**
     * Creates a mock state machine instance for testing purposes.
     *
     * @since [*next-version*]
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function createReadableStateMachine()
    {
        $mock = $this->getMockBuilder('Dhii\State\ReadableStateMachineInterface')
                     ->setMethods(['transition', 'canTransition', 'getState'])
                     ->getMockForAbstractClass();

        return $mock;
    }

    /**
     * Creates an exception mock for an exception interface.
     *
     * @since [*next-version*]
     *
     * @param string $name      The name of the exception mock class.
     * @param string $interface The name of the interface.
     *
     * @return PHPUnit_Framework_MockObject_MockBuilder
     */
    public function createExceptionInterfaceMock($name, $interface)
    {
        eval(sprintf('abstract class %1$s extends Exception implements %2$s {}', $name, $interface));

        return $this->getMockBuilder($name);
    }

    /**
     * Creates a state machine exception.
     *
     * @since [*next-version*]
     *
     * @param string         $msg
     * @param int            $code
     * @param Exception|null $prev
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function createStateMachineException($msg = '', $code = 0, Exception $prev = null)
    {
        $mockBuilder = $this->createExceptionInterfaceMock(
            'StateMachineException',
            'Dhii\State\Exception\StateMachineExceptionInterface'
        );

        $mockBuilder->setMethods(['getStateMachine']);
        $mockBuilder->setConstructorArgs([$msg, $code, $prev]);

        return $mockBuilder->getMockForAbstractClass();
    }

    /**
     * Creates a state machine exception.
     *
     * @since [*next-version*]
     *
     * @param string         $msg
     * @param int            $code
     * @param Exception|null $prev
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function createCouldNotTransitionException($msg = '', $code = 0, Exception $prev = null)
    {
        $mockBuilder = $this->createExceptionInterfaceMock(
            'CouldNotTransitionException',
            'Dhii\State\Exception\CouldNotTransitionExceptionInterface'
        );

        $mockBuilder->setMethods(['getStateMachine', 'getTransition']);
        $mockBuilder->setConstructorArgs([$msg, $code, $prev]);

        return $mockBuilder->getMock();
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInternalType(
            'object',
            $subject,
            'A valid instance of the test subject could not be created.'
        );
    }

    /**
     * Tests the transition method to ensure that the returned state machine is a result of the state machine's
     * transition.
     *
     * @since [*next-version*]
     */
    public function testTransition()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $stateAware = $this->createStateAware();
        $transition = uniqid('transition-');

        // Create a mock state machine and expect the subject to return it
        $stateMachine = $this->createStateMachine();
        $subject->expects($this->once())
                ->method('_getStateMachineFor')
                ->with($stateAware, $transition)
                ->willReturn($stateMachine);

        // Expect transition normalization before transition
        $nTransition = uniqid('transition-');
        $subject->expects($this->once())
                ->method('_normalizeTransition')
                ->with($stateAware, $transition)
                ->willReturn($nTransition);

        // Expect the transition to return a new readable state machine
        $rStateMachine = $this->createReadableStateMachine();
        $stateMachine->expects($this->once())
                     ->method('transition')
                     ->with($nTransition)
                     ->willReturn($rStateMachine);

        // Create mock result and expect subject to return it
        $newSubject = $this->createStateAware();
        $subject->expects($this->once())
                ->method('_getNewSubject')
                ->with($stateAware, $transition, $rStateMachine)
                ->willReturn($newSubject);

        $result = $reflect->_transition($stateAware, $transition);

        $this->assertSame($newSubject, $result, 'Expected and retrieved results are not the same.');
    }

    /**
     * Tests the state machine transitioner method to ensure that an exception is thrown if the resulting state machine
     * is not readable.
     *
     * @since [*next-version*]
     */
    public function testTransitionStateMachineNotReadable()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $stateAware = $this->createStateAware();
        $transition = uniqid('transition-');

        // Create a mock state machine and expect the subject to return it
        $stateMachine = $this->createStateMachine();
        $subject->expects($this->once())
                ->method('_getStateMachineFor')
                ->with($stateAware, $transition)
                ->willReturn($stateMachine);

        // Expect transition normalization before transition
        $nTransition = uniqid('transition-');
        $subject->expects($this->once())
                ->method('_normalizeTransition')
                ->with($stateAware, $transition)
                ->willReturn($nTransition);

        // Expect the transition to return a NON-readable state machine
        $newStateMachine = $this->createStateMachine();
        $stateMachine->expects($this->once())
                     ->method('transition')
                     ->with($nTransition)
                     ->willReturn($newStateMachine);

        $this->setExpectedException('Exception');

        $reflect->_transition($stateAware, $transition);
    }

    /**
     * Tests the transition method with a null state machine retrieved internally to test error handling.
     *
     * @since [*next-version*]
     */
    public function testTransitionNullStateMachine()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);

        $transition = uniqid('transition-');
        $stateAware = $this->createStateAware();

        // Mock and expect the state machine getter to return a state machine
        // when given the argument subject and normalized transition.
        $subject->expects($this->once())
                ->method('_getStateMachineFor')
                ->with($stateAware, $transition)
                ->willReturn(null);

        $this->setExpectedException('Exception');

        $reflect->_transition($stateAware, $transition);
    }

    /**
     * Tests the transition method when a state-machine exception is thrown is test error handling.
     *
     * @since [*next-version*]
     */
    public function testTransitionStateMachineException()
    {
        $subject = $this->createInstance(['_doStateMachineTransition']);
        $reflect = $this->reflect($subject);

        $stateAware = $this->createStateAware();
        $transition = uniqid('transition-');

        // Create a mock state machine and expect the subject to return it
        $stateMachine = $this->createStateMachine();
        $subject->expects($this->once())
                ->method('_getStateMachineFor')
                ->with($stateAware, $transition)
                ->willReturn($stateMachine);

        // Expect transition normalization before transition
        $nTransition = uniqid('transition-');
        $subject->expects($this->once())
                ->method('_normalizeTransition')
                ->with($stateAware, $transition)
                ->willReturn($nTransition);

        // Expect the state machine to throw a state-machine exception
        $smException = $this->createStateMachineException();
        $stateMachine->expects($this->once())
                     ->method('transition')
                     ->with($nTransition)
                     ->willThrowException($smException);

        try {
            $reflect->_transition($stateAware, $transition);

            $this->fail('Expected exception was not thrown.');
        } catch (\Exception $e) {
            $this->assertSame(
                $smException,
                $e->getPrevious(),
                'Previous exception is not the state machine exception.'
            );
        }
    }

    /**
     * Tests the transition method when a could-not-transition exception is thrown is test error handling.
     *
     * @since [*next-version*]
     */
    public function testTransitionCouldNotTransitionException()
    {
        $subject = $this->createInstance(['_doStateMachineTransition']);
        $reflect = $this->reflect($subject);

        $stateAware = $this->createStateAware();
        $transition = uniqid('transition-');

        // Create a mock state machine and expect the subject to return it
        $stateMachine = $this->createStateMachine();
        $subject->expects($this->once())
                ->method('_getStateMachineFor')
                ->with($stateAware, $transition)
                ->willReturn($stateMachine);

        // Expect transition normalization before transition
        $nTransition = uniqid('transition-');
        $subject->expects($this->once())
                ->method('_normalizeTransition')
                ->with($stateAware, $transition)
                ->willReturn($nTransition);

        // Expect the state machine to throw a could-not-transition-machine exception
        $cntException = $this->createCouldNotTransitionException();
        $stateMachine->expects($this->once())
                     ->method('transition')
                     ->with($nTransition)
                     ->willThrowException($cntException);

        try {
            $reflect->_transition($stateAware, $transition);

            $this->fail('Expected exception was not thrown.');
        } catch (\Exception $e) {
            $this->assertSame(
                $cntException,
                $e->getPrevious(),
                'Previous exception is not the state machine exception.'
            );
        }
    }
}
