<?php
	declare(strict_types = 1);

	use Edde\Api\Application\IApplication;
	use Edde\Api\File\IRootDirectory;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\Runtime\Runtime;
	use Tracy\Debugger;

	require_once __DIR__ . '/loader.php';

	Debugger::enable(Debugger::DEVELOPMENT, __DIR__ . '/logs');
	Debugger::$strictMode = true;
	Debugger::$showBar = true;
	Debugger::$onFatalError[] = function ($e) {
		Debugger::log($e);
	};

	/** @noinspection UsingInclusionOnceReturnValueInspection */
	/** @noinspection UsingInclusionReturnValueInspection */
	/** @noinspection PhpUsageOfSilenceOperatorInspection */
	$factoryList = array_merge([
		IRootDirectory::class => new RootDirectory(__DIR__),
	], is_array($local = @include_once __DIR__ . '/loader.local.php') ? $local : []);

	Runtime::execute(SandboxSetup::create($factoryList), function (IApplication $application) {
		$application->run();
	});
