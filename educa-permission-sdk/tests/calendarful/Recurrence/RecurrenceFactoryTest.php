<?php

namespace StuPla\CloudSDK\calendarful\Recurrence;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use StuPla\CloudSDK\calendarful\Recurrence\Type\Daily;

class RecurrenceFactoryTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

	public function testRecurrenceTypeClassDoesNotExist()
	{
        $this->expectException(\InvalidArgumentException::class);
		$recurrenceFactory = new RecurrenceFactory();

		$recurrenceFactory->addRecurrenceType('test', 'ThisIsNotAValidFileOrFilePath');
	}

	public function testRecurrenceTypeClassPathNotRecurrenceInterfaceImplementation()
	{
        $this->expectException(\InvalidArgumentException::class);
		$recurrenceFactory = new RecurrenceFactory();

		$recurrenceFactory->addRecurrenceType('test', 'StuPla\CloudSDK\calendarful\Mocks\MockEvent');
	}

	public function testRecurrenceTypeClassNotRecurrenceInterfaceImplementation()
	{
        $this->expectException(\InvalidArgumentException::class);
		$recurrenceFactory = new RecurrenceFactory();

		$recurrenceFactory->addRecurrenceType('test', new \stdClass());
	}

	public function testValidRecurrenceTypeClassPath()
	{
		$recurrenceFactory = new RecurrenceFactory();

		$recurrenceFactory->addRecurrenceType('test', 'StuPla\CloudSDK\calendarful\Recurrence\Type\Daily');

		$this->assertEquals(1, count($recurrenceFactory->getRecurrenceTypes()));
	}

	public function testValidRecurrenceTypeClass()
	{
		$recurrenceFactory = new RecurrenceFactory();

		$recurrenceFactory->addRecurrenceType('test', new Daily());

		$this->assertEquals(1, count($recurrenceFactory->getRecurrenceTypes()));
	}

    public function testNonExistentRecurrenceTypeClassRetrieval()
	{
        $this->expectException(\OutOfBoundsException::class);
        $recurrenceFactory = new RecurrenceFactory();

		$recurrence = $recurrenceFactory->createRecurrenceType('test');
	}

	public function testValidRecurrenceTypeClassRetrieval()
	{
		$recurrenceFactory = new RecurrenceFactory();

		$recurrenceFactory->addRecurrenceType('daily', new Daily());

		$this->assertInstanceOf('StuPla\CloudSDK\calendarful\Recurrence\RecurrenceInterface', $recurrenceFactory->createRecurrenceType('daily'));
	}
}
