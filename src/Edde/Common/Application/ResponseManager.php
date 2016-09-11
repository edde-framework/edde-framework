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

		public function response(IResponse $response): IResponseManager {
			$this->response = $response;
			return $this;
		}

		public function getResponse(): IResponse {
			$this->use();
			return $this->response;
		}

		protected function prepare() {
			if ($this->response === null) {
				$this->response = new Response();
			}
		}
	}
