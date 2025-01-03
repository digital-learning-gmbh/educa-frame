<?php

namespace StuPla\CloudSDK\calendarful\Calendar;

/**
 * Interface CalendarFactoryInterface
 *
 * An interface for different calendar factories specifying they need to
 * provide implementations for getting the type/s.
 *
 * @package StuPla\CloudSDK\calendarful
 * @abstract
 */
interface CalendarFactoryInterface
{
	/**
	 * Instantiates a calendar and returns it.
	 *
	 * @param string				$type
	 * @return CalendarInterface
	 * @abstract
	 */
	public function createCalendar($type);

	/**
	 * Get all of the calendar type class names from the factory.
	 *
	 * @return string[]
	 * @abstract
	 */
	public function getCalendarTypes();
}
