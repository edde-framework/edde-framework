<?php
	declare(strict_types = 1);

	use Edde\Common\Runtime\Runtime;
	use Tracy\Debugger;

	$factoryList = require __DIR__ . '/loader.php';

	Debugger::enable(Debugger::DEVELOPMENT, __DIR__ . '/logs');
	Debugger::$strictMode = true;
	Debugger::$showBar = true;
	Debugger::$onFatalError[] = function ($e) {
		Debugger::log($e);
	};

	(new Runtime($factoryList))->run(function () {
		echo "hello new Edde Framework!";
	});
