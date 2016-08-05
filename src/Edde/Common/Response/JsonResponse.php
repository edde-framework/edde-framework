<?php
	declare(strict_types = 1);

	namespace Edde\Common\Response;

	class JsonResponse extends AbstractResponse {
		/**
		 * @var mixed
		 */
		protected $json;

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
