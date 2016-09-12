<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Http\IHttpResponse;
	use Edde\Common\Converter\AbstractConverter;

	class ArrayConverter extends AbstractConverter {
		/**
		 * @var IHttpResponse
		 */
		protected $httpResponse;

		public function __construct() {
			parent::__construct([
				'array',
			]);
		}

		public function lazyHttpResponse(IHttpResponse $httpResponse) {
			$this->httpResponse = $httpResponse;
		}

		public function convert($source, string $target) {
			if (is_array($source) === false) {
				$this->unsupported($source, $target);
			}
			switch ($target) {
				case 'http+json':
				case 'http+application/json':
					$this->httpResponse->send();
				case 'json':
				case 'application/json':
					echo json_encode($source);
					return null;
			}
			$this->exception($target);
		}
	}
