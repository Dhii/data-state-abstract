<?php

namespace Dhii\Data\FuncTest;

use \InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use stdClass;
use Xpmock\TestCase;
use Dhii\Data\StateAwareAwareTrait as TestSubject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class StateAwareAwareTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Data\StateAwareAwareTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return MockObject
     */
    public function createInstance()
    {
        // Create mock
        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
                     ->setMethods(['__', '_createInvalidArgumentException'])
                     ->getMockForTrait();

        $mock->method('__')->willReturnArgument(0);
        $mock->method('_createInvalidArgumentException')->willReturnCallback(
            function ($msg = '', $code = 0, $prev = null) {
                return new InvalidArgumentException($msg, $code, $prev);
            }
        );

        return $mock;
    }

    public function createStateAware()
    {
        return $this->mock('Dhii\Data\StateAwareInterface')
                    ->getState()
                    ->new();
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
    public function testGetSetStateAware()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);
        $input   = $this->createStateAware();

        $reflect->_setStateAware($input);

        $this->assertSame($input, $reflect->_getStateAware(), 'Set and retrieved value are not the same.');
    }

    /**
     * Tests the getter and setter methods with a null value to assert whether it is accepted.
     *
     * @since [*next-version*]
     */
    public function testGetSetStateAwareNull()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);
        $input   = null;

        $reflect->_setStateAware($input);

        $this->assertNull($reflect->_getStateAware(), 'Retrieved value is not null.');
    }

    /**
     * Tests the getter and setter methods with an invalid value to assert whether an exception is thrown.
     *
     * @since [*next-version*]
     */
    public function testGetSetStateAwareInvalid()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);
        $input   = new stdClass();

        $this->setExpectedException('InvalidArgumentException');

        $reflect->_setStateAware($input);
    }
}
