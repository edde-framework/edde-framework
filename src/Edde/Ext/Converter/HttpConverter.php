<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Common\Converter\AbstractConverter;

	/**
	 * Basic http converter; it will convert http+text/plain and http+callback to output.
	 */
	class HttpConverter extends AbstractConverter {
		use LazyHttpResponseTrait;

		/**
		 * It is so cold outside I saw a politician with his hands in his own pockets.
		 */
		public function __construct() {
			parent::__construct([
				'text/plain',
				'string',
				'callback',
			]);
		}

		/**
		 * @inheritdoc
		 * @throws ConverterException
		 */
		public function convert($source, string $target) {
			if (is_callable($source) === false && is_string($source) === false) {
				$this->unsupported($source, $target);
			}
			switch ($target) {
				/** @noinspection PhpMissingBreakStatementInspection */
				case 'http+text/plain':
					$this->httpResponse->send();
				case 'text/plain':
					if (is_callable($source)) {
						$source();
						return null;
					}
					echo $source;
					return null;
				case 'string':
					if (is_callable($source)) {
						return $source();
					}
					return $source;
			}
			$this->exception($target);
		}
	}
