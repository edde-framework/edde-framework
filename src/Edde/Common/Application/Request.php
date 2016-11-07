<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\ApplicationException;
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
		protected $action;
		/**
		 * @var array
		 */
		protected $handle;
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
		 */
		public function __construct(string $type) {
			$this->type = $type;
		}

		public function getType(): string {
			return $this->type;
		}

		public function getParameterList(): array {
			return $this->parameterList;
		}

		public function registerActionHandler(string $control, string $action, array $parameterList = []): IRequest {
			$this->action = [
				$control,
				$action,
				$parameterList,
			];
			return $this;
		}

		public function hasAction(): bool {
			return $this->action !== null;
		}

		public function getAction(): array {
			return $this->action;
		}

		public function registerHandleHandler(string $control, string $handle, array $parameterList = []): IRequest {
			$this->handle = [
				$control,
				$handle,
				$parameterList,
			];
			return $this;
		}

		public function hasHandle(): bool {
			return $this->handle !== null;
		}

		public function getHandle(): array {
			return $this->handle;
		}

		public function getCall(): array {
			if ($this->hasHandle()) {
				return $this->getHandle();
			} else if ($this->hasAction()) {
				return $this->getAction();
			}
			throw new ApplicationException(sprintf('Request has no action or handle. Ooops!'));
		}

		public function getId(): string {
			if ($this->id === null) {
				$this->id = hash('sha256', json_encode($this->handlerList));
			}
			return $this->id;
		}
	}
