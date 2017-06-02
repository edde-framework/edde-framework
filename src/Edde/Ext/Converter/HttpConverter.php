<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Api\Converter\ConverterException;
	use Edde\Api\Converter\IContent;
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
				'text/plain',
				'string',
			]);
			$this->register('post', 'array');
		}

		/**
		 * @inheritdoc
		 * @throws ConverterException
		 */
		public function convert($content, string $mime, string $target = null): IContent {
			switch ($mime) {
				case 'text/plain':
				case 'string':
				case 'callback':
					switch ($target) {
						case 'text/plain':
						case 'string':
							if (is_callable($content)) {
								$content = $content();
							}
							return $content;
						default:
							$this->unsupported($content, $target);
					}
					break;
				case 'post':
					switch ($target) {
						case 'array':
							return $content;
						default:
							$this->unsupported($content, $target);
					}
			}
			return $this->exception($mime, $target);
		}
	}
