<?php
	use Edde\Api\Application\IApplication;
	use Edde\Common\Runtime\Runtime;
	use Edde\Ext\Runtime\DefaultSetupHandler;
	use Tracy\Debugger;

	require_once(__DIR__ . '/vendor/autoload.php');
	require_once(__DIR__ . '/../src/loader.php');

	Debugger::enable(Debugger::DEVELOPMENT, __DIR__ . '/logs');
	Debugger::$strictMode = true;

	Runtime::execute(DefaultSetupHandler::create(), function (IApplication $application) {
		$application->run();
	});
