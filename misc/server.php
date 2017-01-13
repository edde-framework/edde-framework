<?php
	declare(strict_types=1);

	file_put_contents(__DIR__ . 'server.php/' . basename(__FILE__) . '.pid', getmypid());
	popen(sprintf('%s -S %s -t %s index.php', escapeshellarg(PHP_BINARY), '127.0.0.127:62080', escapeshellarg(__DIR__)), 'r');

	//	taskkill /F /T /PID
