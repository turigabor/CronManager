<?php

class CronManager {

	private $list = array();
	private $content = '';

	public function setContent($str)
	{
		$this->content = $str;
		$this->parse();
	}

	public function loadFile($fileName)
	{
		$this->content = file_get_contents($fileName);
		$this->parse();
	}

	private function parse()
	{
		$this->list = array();
		$lines = explode("\n", $this->content);
		foreach ($lines as $line) {
			$line = trim($line);
			if (empty($line) or substr($line, 0, 1) === '#') {
				continue;
			}
			$p = preg_split('/\s+/', $line, 6);
			if (count($p) !== 6) {
				throw new Exception('Parser error: ' . $line);
			}
			$entry = new CronEntry($p[0], $p[1], $p[2], $p[3], $p[4], $p[5]);
			$this->list[] = $entry;
		}
	}

	public function getList()
	{
		return $this->list;
	}

}
