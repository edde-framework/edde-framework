<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IResponse;
	use Edde\Common\AbstractObject;

	class Response extends AbstractObject implements IResponse {
		/**
		 * @var string
		 */
		protected $type;
		protected $response;
		/**
		 * @var string
		 */
		protected $mime;

		/**
		 * Response constructor.
		 *
		 * @param string $type
		 * @param $response
		 * @param string $mime
		 */
		public function __construct(string $type = null, $response = null, string $mime = null) {
			$this->type = $type;
			$this->response = $response;
			$this->mime = $mime;
		}

		public function getType(): string {
			return $this->type;
		}

		public function getResponse() {
			return $this->response;
		}

		public function getMime(): string {
			return $this->mime;
		}
	}
