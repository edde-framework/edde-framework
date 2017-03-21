<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\ApplicationException;
	use Edde\Api\Application\IRequest;
	use Edde\Api\Converter\IContent;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Common\Object;

	/**
	 * Application request is responsible for the exact action execution in the application, usually invoking
	 * some control and executing method on it.
	 */
	class Request extends Object implements IRequest {
		use LazyConverterManagerTrait;
		/**
		 * @var IContent
		 */
		protected $content;
		/**
		 * @var string
		 */
		protected $control;
		/**
		 * @var string
		 */
		protected $action;
		/**
		 * @var array
		 */
		protected $parameterList;
		/**
		 * @var string
		 */
		protected $id;

		/**
		 * What is the difference between a snowman and a snowwoman?
		 * -
		 * Snowballs.
		 *
		 * @param string   $control
		 * @param string   $action
		 * @param array    $parameterList
		 * @param IContent $content
		 */
		public function __construct(string $control, string $action, array $parameterList = [], IContent $content = null) {
			$this->control = $control;
			$this->action = $action;
			$this->parameterList = $parameterList;
			$this->content = $content;
		}

		/**
		 * @inheritdoc
		 */
		public function getId(): string {
			if ($this->id === null) {
				$this->id = hash('sha256', json_encode($this->control . $this->action));
			}
			return $this->id;
		}

		/**
		 * @inheritdoc
		 */
		public function getControl(): string {
			return $this->control;
		}

		/**
		 * @inheritdoc
		 */
		public function getAction(): string {
			return $this->action;
		}

		/**
		 * @inheritdoc
		 */
		public function getParameterList(): array {
			return $this->parameterList;
		}

		/**
		 * @inheritdoc
		 */
		public function getContent(array $targetList = null) {
			if ($this->content === null) {
				throw new ApplicationException('There is no body with current application request.');
			}
			if ($targetList) {
				return $this->converterManager->content($this->content, $targetList)
					->convert();
			}
			return $this->content;
		}
	}
