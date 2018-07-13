<?php

namespace Dhii\Data\FuncTest;

use \InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use stdClass;
use Xpmock\TestCase;
use Dhii\Data\TransitionAwareTrait as TestSubject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class TransitionAwareTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Data\TransitionAwareTrait';

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
                     ->setMethods(['_normalizeStringable'])
                     ->getMockForTrait();

        return $mock;
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
    public function testGetSetTransition()
    {
        $subject    = $this->createInstance();
        $reflect    = $this->reflect($subject);
        $input      = uniqid('transition-');
        $normalized = uniqid('normalized-');

        $subject->expects($this->once())
                ->method('_normalizeStringable')
                ->with($input)
                ->willReturn($normalized);

        $reflect->_setTransition($input);

        $this->assertEquals($normalized, $reflect->_getTransition(), 'Set and retrieved value are not the same.');
    }

    /**
     * Tests the getter and setter methods to ensure whether null is accepted.
     *
     * @since [*next-version*]
     */
    public function testGetSetTransitionNull()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);
        $input   = null;

        $subject->expects($this->never())
                ->method('_normalizeStringable')
                ->with(null)
                ->willThrowException(new InvalidArgumentException());

        $reflect->_setTransition($input);

        $this->assertNull($reflect->_getTransition(), 'Retrieved value is not null.');
    }

    /**
     * Tests the getter and setter methods with an invalid value to assert whether an exception is thrown.
     *
     * @since [*next-version*]
     */
    public function testGetSetTransitionInvalid()
    {
        $subject = $this->createInstance();
        $reflect = $this->reflect($subject);
        $input   = new stdClass();

        $subject->expects($this->once())
                ->method('_normalizeStringable')
                ->with($input)
                ->willThrowException(new InvalidArgumentException());

        $this->setExpectedException('InvalidArgumentException');

        $reflect->_setTransition($input);
    }
}
