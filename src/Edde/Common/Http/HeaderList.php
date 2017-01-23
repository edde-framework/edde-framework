<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IContentType;
	use Edde\Api\Http\IHeaderList;
	use Edde\Common\Collection\AbstractList;

	/**
	 * Simple header list implementation over an array.
	 */
	class HeaderList extends AbstractList implements IHeaderList {
		/**
		 * @var IContentType
		 */
		protected $contentType;

		/**
		 * @inheritdoc
		 */
		public function getContentType(): IContentType {
			if ($this->contentType === null) {
				$this->contentType = new ContentType($this->get('Content-Type', ''));
			}
			return $this->contentType;
		}

		/**
		 * @inheritdoc
		 */
		public function getUserAgent(string $default = ''): string {
			return $this->get('User-Agent', $default);
		}

		/**
		 * @inheritdoc
		 */
		public function getAcceptList(): array {
			return HttpUtils::accept($this->get('Accept'));
		}

		/**
		 * @inheritdoc
		 */
		public function getAcceptLanguage(string $default): string {
			return $this->getAccpetLanguageList($default)[0];
		}

		/**
		 * @inheritdoc
		 */
		public function getAccpetLanguageList(string $default): array {
			return HttpUtils::language($this->get('Accept-Language'), $default);
		}

		/**
		 * @inheritdoc
		 */
		public function getAcceptCharset(string $default): string {
			return $this->getAcceptCharsetList($default)[0];
		}

		/**
		 * @inheritdoc
		 */
		public function getAcceptCharsetList(string $default): array {
			return HttpUtils::charset($this->get('Accept-Charset'), $default);
		}

		/**
		 * @inheritdoc
		 */
		public function headers(): array {
			$headers = [];
			foreach ($this->list as $header => $value) {
				$headers[$header] = $header . ': ' . $value;
			}
			return $headers;
		}
	}
