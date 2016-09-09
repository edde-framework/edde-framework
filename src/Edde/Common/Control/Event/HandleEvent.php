<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control\Event;

	use Edde\Api\Control\IControl;
	use Edde\Api\Crate\ICrate;

	/**
	 * This event should be emitted at the beginning of control handle method.
	 */
	class HandleEvent extends ControlEvent {
		/**
		 * @var string
		 */
		protected $method;
		/**
		 * @var array
		 */
		protected $parameterList;
		/**
		 * @var ICrate[]
		 */
		protected $crateList;

		/**
		 * @param IControl $control
		 * @param string $method
		 * @param array $parameterList
		 * @param ICrate[] $crateList
		 */
		public function __construct(IControl $control, $method, array $parameterList, array $crateList) {
			parent::__construct($control);
			$this->method = $method;
			$this->parameterList = $parameterList;
			$this->crateList = $crateList;
		}

		public function getMethod(): string {
			return $this->method;
		}

		public function getParameterList(): array {
			return $this->parameterList;
		}

		/**
		 * @return ICrate[]
		 */
		public function getCrateList(): array {
			return $this->crateList;
		}
	}
