<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IResponse;
	use Edde\Api\Application\IResponseManager;
	use Edde\Common\Usable\AbstractUsable;

	class ResponseManager extends AbstractUsable implements IResponseManager {
		/**
		 * @var IResponse
		 */
		protected $response;
		/**
		 * @var string
		 */
		protected $mime;

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
			$this->use();
		}

		protected function prepare() {
		}
	}
