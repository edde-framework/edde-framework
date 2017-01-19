<?php
	declare(strict_types=1);

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
			$this->register([
				'text/plain',
				'string',
				'callback',
			], [
				'http',
				'http+text/plain',
				'text/plain',
				'string',
				'http+string',
			]);
		}

		/** @noinspection PhpInconsistentReturnPointsInspection */
		/**
		 * @inheritdoc
		 * @throws ConverterException
		 */
		public function convert($convert, string $mime, string $target) {
			if (is_callable($convert) === false && is_string($convert) === false) {
				$this->unsupported($convert, $target);
			}
			switch ($target) {
				case 'http':
				case 'http+text/plain':
					/** @noinspection PhpMissingBreakStatementInspection */
				case 'http+string':
					$headerList = $this->httpResponse->getHeaderList();
					if ($headerList->has('Content-Type') === false) {
						$this->httpResponse->header('Content-Type', 'text/plain');
					}
					$this->httpResponse->send();
				case 'text/plain':
				case 'string':
					if (is_callable($convert)) {
						$convert = $convert();
					}
					echo $convert;
					return null;
			}
			$this->exception($mime, $target);
		}
	}
