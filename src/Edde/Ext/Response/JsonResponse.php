<?php
	namespace Edde\Ext\Response;

	use Edde\Common\Response\AbstractResponse;

	class JsonResponse extends AbstractResponse {
		/**
		 * @var mixed
		 */
		private $json;

		/**
		 * @param mixed $json must be jsonable argument
		 */
		public function __construct($json) {
			$this->json = $json;
		}

		public function send() {
			echo json_encode($this->json);
		}
	}
