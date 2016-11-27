<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime\Event;

	use Edde\Api\Container\IContainer;

	/**
	 * Event emitted right after run method of runtime.
	 */
	class ShutdownEvent extends RuntimeEvent {
		/**
		 * @var IContainer
		 */
		protected $container;
		protected $result;

		/**
		 * @param IContainer $container
		 */
		public function __construct(IContainer $container, $result) {
			$this->container = $container;
			$this->result = $result;
		}

		/**
		 * @return IContainer
		 */
		public function getContainer(): IContainer {
			return $this->container;
		}

		public function getResult() {
			return $this->result;
		}
	}
