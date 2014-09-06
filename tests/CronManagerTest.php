<?php

include __DIR__ . '/../src/CronManager.php';
include __DIR__ . '/../src/CronEntry.php';

class CronManagerTest extends PHPUnit_Framework_TestCase {

	private $cron;

	protected function setUp()
	{
		$this->cron = new CronManager();
	}

	public function testEmptyList()
	{
		$this->assertCount(0, $this->cron->getList());
	}

	public function testCommentLine()
	{
		$this->cron->setContent('# Hello World!');
		$this->assertCount(0, $this->cron->getList());
	}

	public function testAlways()
	{
		$this->cron->setContent('* * * * * Hello World!');
		$list = $this->cron->getList();
		$this->assertCount(1, $list);
		$this->assertEquals('Hello World!', $list[0]->getCommandIfActive());
	}

	private function getEntryByCronTabString($str)
	{
		$this->cron->setContent($str);
		$list = $this->cron->getList();
		return $list[0];
	}

	public function testMinute()
	{
		$entry = $this->getEntryByCronTabString('10 * * * * Hello World!');
		$entry->setTime(mktime(0, 10));
		$this->assertTrue($entry->isActive());
		$this->assertNotNull($entry->getCommandIfActive());
		$entry->setTime(mktime(0, 11));
		$this->assertFalse($entry->isActive());
		$this->assertNull($entry->getCommandIfActive());
	}

	public function testHour()
	{
		$entry = $this->getEntryByCronTabString('5 10 * * * Hello World!');
		$entry->setTime(mktime(10, 5));
		$this->assertTrue($entry->isActive());
		$entry->setTime(mktime(11, 5));
		$this->assertFalse($entry->isActive());
	}

	public function testDay()
	{
		$entry = $this->getEntryByCronTabString('0 0 1 * * Hello World!');
		$entry->setTime(mktime(0, 0, 0, 1, 1));
		$this->assertTrue($entry->isActive());
		$entry->setTime(mktime(0, 0, 0, 1, 2));
		$this->assertFalse($entry->isActive());
	}

	public function testMonth()
	{
		$entry = $this->getEntryByCronTabString('0 0 25 12 * Happy christmas! :-)');
		$entry->setTime(mktime(0, 0, 0, 12, 25));
		$this->assertTrue($entry->isActive());
		$entry->setTime(mktime(0, 0, 0, 11, 25));
		$this->assertFalse($entry->isActive());
	}

	public function testMoreSpace()
	{
		$entry = $this->getEntryByCronTabString(' 0   4 * * * Hello World!');
		$entry->setTime(mktime(4, 0));
		$this->assertTrue($entry->isActive());
		$entry->setTime(mktime(5, 0));
		$this->assertFalse($entry->isActive());
	}

	public function testSundayMidnight()
	{
		$entry = $this->getEntryByCronTabString('0 0 * * 0 Hello World!');
		$entry->setTime(mktime(0, 0, 0, 8, 24, 2014));
		$this->assertTrue($entry->isActive());
		$entry->setTime(mktime(0, 0, 0, 8, 25, 2014));
		$this->assertFalse($entry->isActive());
	}

	public function testEverySixHours()
	{
		$entry = $this->getEntryByCronTabString('0 */6 * * * Hello World!');
		$entry->setTime(mktime(6, 0));
		$this->assertTrue($entry->isActive());
		$entry->setTime(mktime(12, 0));
		$this->assertTrue($entry->isActive());
		$entry->setTime(mktime(12, 1));
		$this->assertFalse($entry->isActive());
		$entry->setTime(mktime(13, 0));
		$this->assertFalse($entry->isActive());
	}

	public function testComma()
	{
		$entry = $this->getEntryByCronTabString('1,2,5,10 * * * * Hello World!');
		foreach (array(1, 2, 5, 10) as $n) {
			$entry->setTime(mktime(6, $n));
			$this->assertTrue($entry->isActive());
		}
		foreach (array(3, 4, 6, 7, 8, 9, 11) as $n) {
			$entry->setTime(mktime(6, $n));
			$this->assertFalse($entry->isActive());
		}
	}

	public function testLoadFile()
	{
		$str = '* * * * * Hello World!' . "\n" . '* * * * * Hello World!';
		$input = "data://text/plain;base64," . base64_encode($str);
		$this->cron->loadFile($input);
		$list = $this->cron->getList();
		$this->assertTrue(count($list) === 2);
	}

	/**
	 * @expectedException        Exception
	 * @expectedExceptionMessage Parser error: * * * Foo
	 */
	public function testWrongCronTabString()
	{
		$this->cron->setContent('* * * Foo');
	}

	/**
	 * @expectedException        Exception
	 * @expectedExceptionMessage Invalid input: x
	 */
	public function testWrongCronTabString2()
	{
		$entry = $this->getEntryByCronTabString('x * * * * Hello World!');
		$this->assertFalse($entry->isActive());
	}

}
