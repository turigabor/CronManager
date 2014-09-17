<?php

spl_autoload_register(function ($className) {
	$fileName = __DIR__ . '/../src/' . $className . '.php';
	if (is_file($fileName)) {
		require $fileName;
		return true;
	}
	return false;
});