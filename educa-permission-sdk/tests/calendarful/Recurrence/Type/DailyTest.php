<?php

namespace StuPla\CloudSDK\calendarful\Recurrence\Type;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use StuPla\CloudSDK\calendarful\Mocks\MockEvent;
use StuPla\CloudSDK\calendarful\Mocks\MockRecurrentEvent;

class DailyTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

	public function testOneDayDailyEventStartingWithinDateRange()
	{
		$dailyRecurrenceType = new Daily();

		$mockEvents = array(
			new MockRecurrentEvent(1, '2014-06-01 00:00:00', '2014-06-01 02:00:00', null, null, 'daily'),
		);

		$fromDate = new \DateTime('2014-06-01 00:00:00');
		$toDate = new \DateTime('2014-06-30 23:59:59');

		$generatedDailyOccurrences = $dailyRecurrenceType->generateOccurrences($mockEvents, $fromDate, $toDate);

		$this->assertCount(30, $generatedDailyOccurrences);
	}

	public function testOneDayDailyEventStartingWithinDateRangeWithUntil()
	{
		$dailyRecurrenceType = new Daily();

		$mockEvents = array(
			new MockRecurrentEvent(1, '2014-06-01 00:00:00', '2014-06-01 02:00:00', null, null, 'daily', '2014-06-15 23:59:59'),
		);

		$fromDate = new \DateTime('2014-06-01 00:00:00');
		$toDate = new \DateTime('2014-06-30 23:59:59');

		$generatedDailyOccurrences = $dailyRecurrenceType->generateOccurrences($mockEvents, $fromDate, $toDate);

		$this->assertCount(15, $generatedDailyOccurrences);
	}

	public function testMultipleDayDailyEventStartingWithinDateRange()
	{
		$dailyRecurrenceType = new Daily();

		$mockEvents = array(
			new MockRecurrentEvent(1, '2014-06-01 00:00:00', '2014-06-03 02:00:00', null, null, 'daily'),
		);

		$fromDate = new \DateTime('2014-06-01 00:00:00');
		$toDate = new \DateTime('2014-06-30 23:59:59');

		$generatedDailyOccurrences = $dailyRecurrenceType->generateOccurrences($mockEvents, $fromDate, $toDate);

		$this->assertCount(30, $generatedDailyOccurrences);
	}

	public function testMultipleDayDailyEventStartingWithinDateRangeWithUntil()
	{
		$dailyRecurrenceType = new Daily();

		$mockEvents = array(
			new MockRecurrentEvent(1, '2014-06-01 00:00:00', '2014-06-03 02:00:00', null, null, 'daily', '2014-06-15 23:59:59'),
		);

		$fromDate = new \DateTime('2014-06-01 00:00:00');
		$toDate = new \DateTime('2014-06-30 23:59:59');

		$generatedDailyOccurrences = $dailyRecurrenceType->generateOccurrences($mockEvents, $fromDate, $toDate);

		$this->assertCount(15, $generatedDailyOccurrences);
	}

	public function testOneDayDailyEventStartingBeforeDateRange()
	{
		$dailyRecurrenceType = new Daily();

		$mockEvents = array(
			new MockRecurrentEvent(1, '2014-06-01 00:00:00', '2014-06-01 02:00:00', null, null, 'daily'),
		);

		$fromDate = new \DateTime('2014-05-20 00:00:00');
		$toDate = new \DateTime('2014-06-30 23:59:59');

		$generatedDailyOccurrences = $dailyRecurrenceType->generateOccurrences($mockEvents, $fromDate, $toDate);

		$this->assertCount(30, $generatedDailyOccurrences);
	}

	public function testMultipleDayDailyEventStartingBeforeDateRange()
	{
		$dailyRecurrenceType = new Daily();

		$mockEvents = array(
			new MockRecurrentEvent(1, '2014-06-01 00:00:00', '2014-06-03 02:00:00', null, null, 'daily'),
		);

		$fromDate = new \DateTime('2014-05-20 00:00:00');
		$toDate = new \DateTime('2014-06-30 23:59:59');

		$generatedDailyOccurrences = $dailyRecurrenceType->generateOccurrences($mockEvents, $fromDate, $toDate);

		$this->assertCount(30, $generatedDailyOccurrences);
	}

	public function testOneDayDailyEventStartingBeforeDateRangeWithUntil()
	{
		$dailyRecurrenceType = new Daily();

		$mockEvents = array(
			new MockRecurrentEvent(1, '2014-06-01 00:00:00', '2014-06-01 02:00:00', null, null, 'daily', '2014-06-15 23:59:59'),
		);

		$fromDate = new \DateTime('2014-05-20 00:00:00');
		$toDate = new \DateTime('2014-06-30 23:59:59');

		$generatedDailyOccurrences = $dailyRecurrenceType->generateOccurrences($mockEvents, $fromDate, $toDate);

		$this->assertCount(15, $generatedDailyOccurrences);
	}

	public function testMultipleDayDailyEventStartingBeforeDateRangeWithUntil()
	{
		$dailyRecurrenceType = new Daily();

		$mockEvents = array(
			new MockRecurrentEvent(1, '2014-06-01 00:00:00', '2014-06-03 02:00:00', null, null, 'daily', '2014-06-15 23:59:59'),
		);

		$fromDate = new \DateTime('2014-05-20 00:00:00');
		$toDate = new \DateTime('2014-06-30 23:59:59');

		$generatedDailyOccurrences = $dailyRecurrenceType->generateOccurrences($mockEvents, $fromDate, $toDate);

		$this->assertCount(15, $generatedDailyOccurrences);
	}

	public function testDailyEventsLimit()
	{
		$dailyRecurrenceType = new Daily();

		$mockEvents = array(
			new MockRecurrentEvent(1, '2014-06-01 00:00:00', '2014-06-01 01:00:00', null, null, 'daily'),
		);

		$fromDate = new \DateTime('2014-06-01 00:00:00');
		$toDate = new \DateTime('2014-06-30 23:59:59');

		$generatedDailyOccurrences = $dailyRecurrenceType->generateOccurrences($mockEvents, $fromDate, $toDate, 2);

		$this->assertCount(2, $generatedDailyOccurrences);
	}

	public function testDailyEventsMaxEndDate()
	{
		$dailyRecurrenceType = new Daily();

		$mockEvents = array(
			new MockRecurrentEvent(1, '2014-06-01 00:00:00', '2014-06-01 01:00:00', null, null, 'daily'),
		);

		$fromDate = new \DateTime('2014-06-01 00:00:00');
		$toDate = new \DateTime('2016-06-01 00:00:00');

		$generatedDailyOccurrences = $dailyRecurrenceType->generateOccurrences($mockEvents, $fromDate, $toDate);

		$lastOccurrence = end($generatedDailyOccurrences);

		$this->assertEquals(new \DateTime('2015-06-01 00:00:00'), $lastOccurrence->getStartDate());
	}
}
