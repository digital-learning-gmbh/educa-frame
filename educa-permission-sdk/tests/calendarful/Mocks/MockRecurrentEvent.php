<?php

namespace StuPla\CloudSDK\calendarful\Mocks;

use StuPla\CloudSDK\calendarful\Event\RecurrentEventInterface;

class MockRecurrentEvent extends MockEvent implements RecurrentEventInterface
{
	protected $recurrenceType;

    protected $recurrenceUntil;
	protected $reccurenceStart;

	public function __construct($id, $startDate, $endDate, $parentId = null, $occurrenceDate = null, $recurrenceType = null, $recurrenceUntil = null)
	{
		$this->id = $id;
		$this->startDate = new \DateTime($startDate);
		$this->endDate = new \DateTime($endDate);
		$this->parentId = $parentId;
		$this->occurrenceDate = $occurrenceDate ? new \DateTime($occurrenceDate) : null;
		$this->recurrenceType = $recurrenceType;
		$this->recurrenceUntil = $recurrenceUntil ? new \DateTime($recurrenceUntil) : null;
	}

	public function getRecurrenceType()
	{
		return $this->recurrenceType;
	}

	public function setRecurrenceType($type = null)
	{
		if ($type === null) {
			$this->recurrenceUntil = null;
		}

		$this->recurrenceType = $type;
	}

	public function getRecurrenceUntil()
	{
		return $this->recurrenceUntil;
	}

    public function getRecurrenceTurnus()
    {
        return 1;
    }

    public function getRecurrenceStart()
    {
        return $this->reccurenceStart;
    }

    public function setRecurrenceStart($reccurenceStart = null)
    {
        $this->reccurenceStart = $reccurenceStart;
    }
}
