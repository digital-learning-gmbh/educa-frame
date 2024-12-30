<?php

namespace StuPla\CloudSDK\calendarful\Event;

/**
 * Interface EventRegistryInterface
 *
 * Provides an interface for registry implementations that allow the
 * persistence of data.
 *
 * @package StuPla\CloudSDK\calendarful
 * @abstract
 */
interface EventRegistryInterface
{
	/**
	 * Gets data and allows the passing of filters if desired.
	 *
	 * @param	array $filters
	 * @return	EventInterface[]
	 */
	public function getEvents(array $filters = array());

	/**
	 * Gets data and allows the passing of filters if desired.
	 *
	 * @param	array $filters
	 * @return	EventInterface[]
	 */
	public function getRecurrentEvents(array $filters = array());
	
	public function getVactionTimes(array $filters = array());
}
