<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Api\Http\IBody;
	use Edde\Common\AbstractObject;

	class Body extends AbstractObject implements IBody, ILazyInject {
		/**
		 * @var string|callable
		 */
		protected $body;
		/**
		 * @var string
		 */
		protected $mime;
		/**
		 * @var string
		 */
		protected $target;
		/**
		 * @var IConverterManager
		 */
		protected $converterManager;

		public function __construct($body = null, string $mime = '', string $target = '') {
			$this->body = $body;
			$this->mime = $mime;
			$this->target = $target;
		}

		public function lazyConverterManager(IConverterManager $converterManager) {
			$this->converterManager = $converterManager;
		}

		public function convert(string $mime = null, string $target = null) {
			return $this->converterManager->convert($this->getBody(), $mime ?: $this->mime, $this->target ?: $target);
		}

		public function getBody() {
			if (is_callable($this->body)) {
				$this->body = call_user_func($this->body);
			}
			return $this->body;
		}

		public function getMime(): string {
			return $this->mime;
		}

		public function getTarget(): string {
			return $this->target;
		}
	}
