<?php
	declare(strict_types = 1);

	namespace Edde\Module;

	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Log\ILogDirectory;
	use Edde\Api\Log\ILogService;
	use Edde\Common\Log\LogService;
	use Edde\Common\Runtime\AbstractModule;
	use Edde\Common\Runtime\Event\SetupEvent;

	/**
	 * Logging support.
	 */
	class LoggerModule extends AbstractModule {
		public function setupLoggerModule(SetupEvent $setupEvent) {
			$runtime = $setupEvent->getRuntime();
			$runtime->registerFactoryList([
				ILogService::class => LogService::class,
				ILogDirectory::class => function (IRootDirectory $rootDirectory) {
					return $rootDirectory->directory('logs');
				},
			]);
		}
	}
