<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Api\Runtime\ISetupHandler;
	use Edde\Common\Deffered\AbstractDeffered;

	class Runtime extends AbstractDeffered implements IRuntime {
		/**
		 * @var ISetupHandler
		 */
		protected $setupHandler;
		/**
		 * @var IContainer
		 */
		protected $container;

		/**
		 * @param ISetupHandler $setupHandler
		 */
		public function __construct(ISetupHandler $setupHandler) {
			$this->setupHandler = $setupHandler;
		}

		/**
		 * execute the given callback with the given ISetupHandler; automagically register current IRuntime and ISetupHandler into IContainer
		 *
		 * @param ISetupHandler $setupHandler
		 * @param callable $callback
		 *
		 * @return mixed
		 */
		static public function execute(ISetupHandler $setupHandler, callable $callback) {
			$runtime = new self($setupHandler);
			$setupHandler->registerFactoryList([
				IRuntime::class => $runtime,
				ISetupHandler::class => $setupHandler,
			]);
			return $runtime->run($callback);
		}

		public function run(callable $callback) {
			$this->use();
			return $this->container->call($callback);
		}

		public function isConsoleMode() {
			return php_sapi_name() === 'cli';
		}

		protected function prepare() {
			$this->container = $this->setupHandler->createContainer();
		}
	}
