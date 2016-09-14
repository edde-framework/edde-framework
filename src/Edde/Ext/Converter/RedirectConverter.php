<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Http\IHttpResponse;
	use Edde\Common\Converter\AbstractConverter;

	class RedirectConverter extends AbstractConverter {
		/**
		 * @var IHttpResponse
		 */
		protected $httpResponse;

		public function __construct() {
			parent::__construct([
				'redirect',
			]);
		}

		public function lazyHttpResponse(IHttpResponse $httpResponse) {
			$this->httpResponse = $httpResponse;
		}

		public function convert($source, string $target) {
			$this->unsupported($source, $target, is_string($source));
			switch ($target) {
				case 'http+text/html':
					$this->httpResponse->header('Location', $source);
					$this->httpResponse->send();
					return $source;
				case 'http+application/json':
					$this->httpResponse->send();
					echo $source = json_encode(['redirect' => $source]);
					return $source;
			}
			$this->exception($target);
		}
	}
