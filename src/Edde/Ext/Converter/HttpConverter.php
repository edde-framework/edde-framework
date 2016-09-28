<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Common\Converter\AbstractConverter;

	/**
	 * Basic http converter; it will convert http+text/plain and http+callback to output.
	 */
	class HttpConverter extends AbstractConverter {
		use LazyHttpResponseTrait;

		public function __construct() {
			parent::__construct([
				'text/plain',
				'string',
				'callback',
			]);
		}

		public function convert($source, string $target) {
			if (is_callable($source) === false && is_string($source) === false) {
				$this->unsupported($source, $target);
			}
			switch ($target) {
				case 'http+text/plain':
					$this->httpResponse->send();
					if (is_callable($source)) {
						$source();
						return null;
					}
					echo $source;
					return null;
			}
			$this->exception($target);
		}
	}
