<?php
	declare(strict_types = 1);

	use Edde\Common\Stream\StreamClient;

	require_once __DIR__ . '/loader.php';

	$client = new StreamClient();
	$client->connect('tcp://127.0.0.1:389');
	$client->write(str_repeat('0', 1024 * 1024 * 32));
	$client->close();
