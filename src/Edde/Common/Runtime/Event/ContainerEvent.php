<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime\Event;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Runtime\IRuntime;

	/**
	 * Container is built, this is called before run method of runtime. This is time to define deffereds.
	 */
	class ContainerEvent extends RuntimeEvent {
		/**
		 * @var IRuntime
		 */
		protected $runtime;
		/**
		 * @var IContainer
		 */
		protected $container;

		/**
		 * @param IRuntime   $runtime
		 * @param IContainer $container
		 */
		public function __construct(IRuntime $runtime, IContainer $container) {
			$this->runtime = $runtime;
			$this->container = $container;
		}

		/**
		 * @return IRuntime
		 */
		public function getRuntime(): IRuntime {
			return $this->runtime;
		}

		/**
		 * @return IContainer
		 */
		public function getContainer(): IContainer {
			return $this->container;
		}
	}
