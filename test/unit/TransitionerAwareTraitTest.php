<?php

namespace Dhii\Data\Transitioner\FuncTest;

use \InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;
use Dhii\Data\TransitionerInterface;
use stdClass;
use Xpmock\TestCase;

/**
 * Tests {@see Dhii\Data\Transitioner\TransitionerAwareTrait}.
 *
 * @since [*next-version*]
 */
class TransitionerAwareTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Data\TransitionerAwareTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function createInstance()
    {
        // Create mock
        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                     ->setMethods(['__', '_createInvalidArgumentException'])
                     ->getMockForTrait();

        $mock->method('__')->willReturnArgument(0);
        $mock->method('_createInvalidArgumentException')->willReturnCallback(
            function($msg = '', $code = 0, $prev = null) {
                return new InvalidArgumentException($msg, $code, $prev);
            }
        );

        return $mock;
    }

    /**
     * Creates a mock booking transitioner instance.
     *
     * @since [*next-version*]
     *
     * @return TransitionerInterface
     */
    public function createTransitioner()
    {
        $mock = $this->mock('Dhii\Data\TransitionerInterface')
                     ->transition();

        return $mock->new();
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
            'An instance of the test subject could not be created'
        );
    }

    /**
     * Tests the getter and setter methods to ensure correct assignment and retrieval.
     *
     * @since [*next-version*]
     */
    public function testGetSetTransitioner()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);
        $input   = $this->createTransitioner();

        $reflect->_setTransitioner($input);

        $this->assertSame($input, $reflect->_getTransitioner(), 'Set and retrieved value are not the same.');
    }

    /**
     * Tests the getter and setter methods with a null value to ensure correct assignment and retrieval.
     *
     * @since [*next-version*]
     */
    public function testGetSetTransitionerNull()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);
        $input   = null;

        $reflect->_setTransitioner($input);

        $this->assertNull($reflect->_getTransitioner(), 'Retrieved transitioner is not null.');
    }

    /**
     * Tests the getter and setter methods with an invalid value to assert whether an exception is thrown.
     *
     * @since [*next-version*]
     */
    public function testGetSetTransitionerInvalid()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);
        $input   = new stdClass();

        $this->setExpectedException('InvalidArgumentException');

        $reflect->_setTransitioner($input);
    }
}
