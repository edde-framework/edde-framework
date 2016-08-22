<?php
	declare(strict_types = 1);

	namespace Edde\Common\Response;

	use Edde\Common\Http\HttpResponse;

	class JsonHttpResponse extends HttpResponse {
		/**
		 * @var array|\stdClass
		 */
		protected $json;

		public function __construct($json) {
			parent::__construct();
			$this->json = $json;
			$this->headerList->set('Content-Type', 'application/json');
			$this->setRenderCallback([
				$this,
				'send',
			]);
		}

		public function send() {
			echo json_encode($this->json);
		}
	}
