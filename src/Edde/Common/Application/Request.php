<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IRequest;
	use Edde\Common\AbstractObject;

	class Request extends AbstractObject implements IRequest {
		/**
		 * @var string
		 */
		protected $type;
		/**
		 * @var string
		 */
		protected $class;
		/**
		 * @var string
		 */
		protected $method;
		/**
		 * @var array
		 */
		protected $parameterList = [];

		/**
		 * @param string $type
		 * @param string $class
		 * @param string $method
		 * @param array $parameterList
		 */
		public function __construct(string $type, string $class, string $method, array $parameterList) {
			$this->type = $type;
			$this->class = $class;
			$this->method = $method;
			$this->parameterList = $parameterList;
		}

		public function getType(): string {
			return $this->type;
		}

		public function getClass(): string {
			return $this->class;
		}

		public function getMethod(): string {
			return $this->method;
		}

		public function getParameterList(): array {
			return $this->parameterList;
		}
	}
