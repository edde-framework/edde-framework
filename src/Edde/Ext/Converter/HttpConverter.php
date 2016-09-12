<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Http\IHttpResponse;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Converter\AbstractConverter;

	/**
	 * Basic http converter; it will convert http+text/plain and http+callback to output.
	 */
	class HttpConverter extends AbstractConverter {
		use LazyInjectTrait;
		/**
		 * @var IHttpResponse
		 */
		protected $httpResponse;

		public function __construct() {
			parent::__construct([
				'http+text/plain',
				'http+callback',
			]);
		}

		public function lazyHttpResponse(IHttpResponse $httpResponse) {
			$this->httpResponse = $httpResponse;
		}

		public function convert($source, string $target) {
			switch ($target) {
				case 'http+text/plain':
					if (is_string($source) === false) {
						$this->unsupported($source, $target);
					}
					$this->httpResponse->send();
					echo $source;
					return null;
				case 'http+callback':
					if (is_callable($source) === false) {
						$this->unsupported($source, $target);
					}
					$this->httpResponse->send();
					$source();
					return null;
			}
			$this->exception($target);
		}
	}
