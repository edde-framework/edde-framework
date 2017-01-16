<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\ApplicationException;
	use Edde\Api\Application\IRequest;
	use Edde\Api\Converter\IContent;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Common\Object;
	use Edde\Common\Strings\StringUtils;

	class Request extends Object implements IRequest {
		use LazyConverterManagerTrait;
		/**
		 * @var IContent
		 */
		protected $content;
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
		 * @param IContent $content
		 */
		public function __construct(IContent $content) {
			$this->content = $content;
		}

		/**
		 * @inheritdoc
		 */
		public function getId(): string {
			if ($this->id === null) {
				$action = $this->action;
				$handle = $this->handle;
				unset($action[2], $handle[2]);
				$this->id = hash('sha256', json_encode($action) . json_encode($handle));
			}
			return $this->id;
		}

		/**
		 * @inheritdoc
		 */
		public function getContent(array $targetList = null) {
			if ($targetList) {
				return $this->converterManager->content($this->content, $targetList);
			}
			return $this->content;
		}

		/**
		 * @inheritdoc
		 */
		public function registerActionHandler(string $control, string $action, array $parameterList = []): IRequest {
			$this->action = [
				$control,
				$action,
				$parameterList,
			];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasAction(): bool {
			return $this->action !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function getAction(): array {
			return $this->action;
		}

		/**
		 * @inheritdoc
		 */
		public function getActionName(): string {
			return StringUtils::recamel($this->action[1], '-', 1);
		}

		/**
		 * @inheritdoc
		 */
		public function registerHandleHandler(string $control, string $handle, array $parameterList = []): IRequest {
			$this->handle = [
				$control,
				$handle,
				$parameterList,
			];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasHandle(): bool {
			return $this->handle !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function getHandle(): array {
			return $this->handle;
		}

		/**
		 * @inheritdoc
		 */
		public function getHandleName(): string {
			return StringUtils::recamel($this->handle[1], '-', 1);
		}

		/**
		 * @inheritdoc
		 */
		public function getCurrent(): array {
			if ($this->hasHandle()) {
				return $this->getHandle();
			} else if ($this->hasAction()) {
				return $this->getAction();
			}
			throw new ApplicationException(sprintf('Request has no action or handle. Ooops!'));
		}

		/**
		 * @inheritdoc
		 */
		public function getCurrentName(): string {
			return StringUtils::recamel($this->getCurrent()[1], '-', 1);
		}
	}
