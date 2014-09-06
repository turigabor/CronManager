<?php

class CronEntry {

	private $time;
	private $minute;
	private $hour;
	private $dayOfMonth;
	private $month;
	private $dayOfWeek;
	private $command;

	public function __construct($minute, $hour, $dayOfMonth, $month, $dayOfWeek, $command)
	{
		$this->minute = $minute;
		$this->hour = $hour;
		$this->dayOfMonth = $dayOfMonth;
		$this->month = $month;
		$this->dayOfWeek = $dayOfWeek;
		$this->command = $command;
	}

	public function setTime($time)
	{
		$this->time = $time;
	}

	public function getCommandIfActive()
	{
		if ($this->isActive()) {
			return $this->command;
		}
		return null;
	}

	public function isActive()
	{
		return $this->checkMinute() &&
			$this->checkHour() &&
			$this->checkDayOfMonth() &&
			$this->checkMonth() &&
			$this->checkDayOfWeek();
	}

	private function checkMinute()
	{
		return $this->check($this->minute, 'i');
	}

	private function checkHour()
	{
		return $this->check($this->hour, 'H');
	}

	private function checkDayOfMonth()
	{
		return $this->check($this->dayOfMonth, 'j');
	}

	private function checkMonth()
	{
		return $this->check($this->month, 'n');
	}

	private function checkDayOfWeek()
	{
		return $this->check($this->dayOfWeek, 'w');
	}

	private function check($value1, $format)
	{
		if ($value1 === '*') {
			return true;
		}
		$value2 = (int) date($format, $this->getTime());
		if (is_numeric($value1)) {
			return (int) $value1 === $value2;
		}
		$divider = $this->getDivider($value1);
		if (is_integer($divider)) {
			return $value2 % $divider === 0;
		}
		$numbers = $this->getNumbers($value1);
		if (is_array($numbers)) {
			return in_array($value2, $numbers);
		}
		throw new Exception('Invalid input: ' . $value1);
	}

	private function getDivider($str)
	{
		$t = explode('/', $str);
		if (count($t) === 2 && $t[0] === '*' && is_numeric($t[1])) {
			return (int) $t[1];
		}
		return null;
	}

	private function getNumbers($str)
	{
		$result = array();
		foreach (explode(',', $str) as $number) {
			if (is_numeric($number)) {
				$result[] = (int) $number;
			} else {
				return null;
			}
		}
		return $result;
	}

	private function getTime()
	{
		return $this->time ? $this->time : $_SERVER{'REQUEST_TIME'};
	}

}
