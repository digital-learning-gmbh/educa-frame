<?php

namespace StuPla\CloudSDK\calendarful\Calendar;

use Mockery as m;
use PHPUnit\Framework\TestCase;

class CalendarFactoryTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testCalendarTypeClassDoesNotExist()
	{
        $this->expectException(\InvalidArgumentException::class);
        $calendarFactory = new CalendarFactory();

		$calendarFactory->addCalendarType('test', 'ThisIsNotAValidFileOrFilePath');
	}

	public function testCalendarTypeClassPathNotCalendarInterfaceImplementation()
	{
        $this->expectException(\InvalidArgumentException::class);
		$calendarFactory = new CalendarFactory();

		$calendarFactory->addCalendarType('test', 'StuPla\CloudSDK\calendarful\Mocks\MockEvent');
	}

	public function testCalendarTypeClassNotCalendarInterfaceImplementation()
	{
        $this->expectException(\InvalidArgumentException::class);
		$calendarFactory = new CalendarFactory();

		$calendarFactory->addCalendarType('test', new \stdClass());
	}

	public function testValidCalendarTypeClassPath()
	{
		$calendarFactory = new CalendarFactory();

		$calendarFactory->addCalendarType('test', 'StuPla\CloudSDK\calendarful\Calendar\Calendar');

		$this->assertEquals(1, count($calendarFactory->getCalendarTypes()));
	}

	public function testValidCalendarTypeClass()
	{
		$calendarFactory = new CalendarFactory();

		$calendarFactory->addCalendarType('test', new Calendar());

		$this->assertEquals(1, count($calendarFactory->getCalendarTypes()));
	}

	public function testNonExistentCalendarTypeClassRetrieval()
	{
        $this->expectException(\OutOfBoundsException::class);
		$calendarFactory = new CalendarFactory();

		$calendar = $calendarFactory->createCalendar('test');
	}

	public function testValidCalendarTypeClassRetrieval()
	{
		$calendarFactory = new CalendarFactory();

		$calendarFactory->addCalendarType('test', new Calendar());

		$this->assertInstanceOf('StuPla\CloudSDK\calendarful\Calendar\CalendarInterface', $calendarFactory->createCalendar('test'));
	}
}
