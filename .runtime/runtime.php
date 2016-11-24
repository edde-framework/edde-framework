<?php
	declare(strict_types = 1);

	use Tracy\Debugger;

	require_once __DIR__ . '/loader.php';

	Debugger::enable(Debugger::DEVELOPMENT, __DIR__ . '/logs');
	Debugger::$strictMode = true;
	Debugger::$showBar = true;
	Debugger::$onFatalError[] = function ($e) {
		Debugger::log($e);
	};

	echo "hello";

	//	Runtime::execute(SandboxSetup::create($factoryList), function (IApplication $application) {
	//		$application->run();
	//	});
