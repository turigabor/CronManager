Cron Manager
============

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/turigabor/CronManager/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/turigabor/CronManager/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/turigabor/CronManager/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/turigabor/CronManager/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/turigabor/CronManager/badges/build.png?b=master)](https://scrutinizer-ci.com/g/turigabor/CronManager/build-status/master)

This is a simple Cron Manager. I need a PHP script which processes a cron file.

Run this script every minute:

```php
$cron = new CronManager();
$cron->loadFile('cronjob.txt');
foreach ($cron->getList() as $entry) {
	$command = $entry->getCommandIfActive();
	if ($command) {
		shell_exec($command); // or something...
	}
}
```
