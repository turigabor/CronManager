Cron Manager
============

This is a simple Cron Manager. I need a PHP script which process a cron file.

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
