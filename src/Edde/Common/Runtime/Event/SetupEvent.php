<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime\Event;

	use Edde\Api\Runtime\IRuntime;

	/**
	 * It's time to configure the runtime! In this event should be defined only local deffereds, foreign one should be defined in ContainerEvent.
	 */
	class SetupEvent extends RuntimeEvent {
		/**
		 * @var IRuntime
		 */
		protected $runtime;

		/**
		 * @param IRuntime $runtime
		 */
		public function __construct(IRuntime $runtime) {
			$this->runtime = $runtime;
		}

		public function getRuntime(): IRuntime {
			return $this->runtime;
		}
	}
