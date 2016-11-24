<?php
	declare(strict_types = 1);

	use Edde\Api\Application\IApplication;
	use Edde\Common\CommonSetupHandler;
	use Edde\Common\Runtime\Runtime;
	use Tracy\Debugger;

	$factoryList = require __DIR__ . '/loader.php';

	Debugger::enable(Debugger::DEVELOPMENT, __DIR__ . '/logs');
	Debugger::$strictMode = true;
	Debugger::$showBar = true;
	Debugger::$onFatalError[] = function ($e) {
		Debugger::log($e);
	};

	Runtime::execute(CommonSetupHandler::create($factoryList), function (IApplication $application) {
		$application->run();
	});
