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
		 * @var array
		 */
		protected $handlerList;
		/**
		 * @var array
		 */
		protected $parameterList = [];
		/**
		 * @var string
		 */
		protected $id;

		/**
		 * What is the difference between a snowman and a snowwoman?
		 * -
		 * Snowballs.
		 *
		 * @param string $type
		 * @param array $parameterList
		 */
		public function __construct(string $type, array $parameterList) {
			$this->type = $type;
			$this->parameterList = $parameterList;
		}

		public function getType(): string {
			return $this->type;
		}

		public function getParameterList(): array {
			return $this->parameterList;
		}

		public function registerHandler(string $class, string $method): IRequest {
			$this->handlerList[] = [
				$class,
				$method,
			];
			return $this;
		}

		public function getHandlerList(): array {
			return $this->handlerList;
		}

		public function getId(): string {
			if ($this->id === null) {
				$this->id = hash('sha256', json_encode($this->handlerList));
			}
			return $this->id;
		}
	}
