<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control\Event;

	use Edde\Api\Control\IControl;

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
		 * @param IControl $control
		 * @param string $method
		 * @param array $parameterList
		 */
		public function __construct(IControl $control, $method, array $parameterList) {
			parent::__construct($control);
			$this->method = $method;
			$this->parameterList = $parameterList;
		}

		public function getMethod(): string {
			return $this->method;
		}

		public function getParameterList(): array {
			return $this->parameterList;
		}
	}
