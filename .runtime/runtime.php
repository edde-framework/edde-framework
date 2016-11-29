<?php
	declare(strict_types = 1);

	use Edde\Api\Application\IApplication;
	use Tracy\Debugger;

	$runtime = require __DIR__ . '/loader.php';

	Debugger::enable(Debugger::DEVELOPMENT, __DIR__ . '/logs');
	Debugger::$strictMode = true;
	Debugger::$showBar = true;
	Debugger::$onFatalError[] = function ($e) {
		Debugger::log($e);
	};

	$runtime->run(function (IApplication $application) {
		$application->run();
	});
