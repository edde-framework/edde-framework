<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\IResponse;
	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Converter\IConverterManager;
	use Edde\Common\Usable\AbstractUsable;

	class ResponseManager extends AbstractUsable implements IResponseManager {
		/**
		 * @var IRequest
		 */
		protected $request;
		/**
		 * @var IConverterManager
		 */
		protected $converterManager;
		/**
		 * @var IResponse
		 */
		protected $response;
		/**
		 * @var string
		 */
		protected $mime;

		public function lazyRequest(IRequest $request) {
			$this->request = $request;
		}

		public function lazyConverterManager(IConverterManager $converterManager) {
			$this->converterManager = $converterManager;
		}

		public function response(IResponse $response): IResponseManager {
			$this->response = $response;
			return $this;
		}

		public function getMime(): string {
			return $this->mime;
		}

		public function setMime(string $mime): IResponseManager {
			$this->mime = $mime;
			return $this;
		}

		public function execute() {
			if ($this->response === null) {
				return;
			}
			$this->use();
			$this->converterManager->convert($this->response->getResponse(), $this->response->getType(), $this->mime);
		}

		protected function prepare() {
		}
	}
