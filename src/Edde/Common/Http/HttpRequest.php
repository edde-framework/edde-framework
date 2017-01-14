<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Http\LazyHeaderListTrait;
	use Edde\Api\Http\LazyRequestUrlTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Url\Url;

	class HttpRequest extends AbstractHttp implements IHttpRequest {
		use LazyRequestUrlTrait;
		use LazyHeaderListTrait;
		/**
		 * @var string
		 */
		protected $method;
		/**
		 * @var string|null
		 */
		protected $remoteAddress;
		/**
		 * @var string|null
		 */
		protected $remoteHost;
		/**
		 * @var IHttpResponse
		 */
		protected $response;
		/**
		 * @var IUrl
		 */
		protected $referrer;

		public function getMethod(): string {
			return $_SERVER['REQUEST_METHOD'] ?? '';
		}

		public function isMethod(string $method): bool {
			return strcasecmp($this->getMethod(), $method) === 0;
		}

		public function getRemoteAddress() {
			return $this->remoteAddress;
		}

		public function getRemoteHost() {
			if ($this->remoteHost === null && $this->remoteAddress !== null) {
				$this->remoteHost = gethostbyaddr($this->remoteAddress);
			}
			return $this->remoteHost;
		}

		public function getReferrer() {
			if ($this->referrer === null && $this->headerList->has('referer')) {
				$this->referrer = new Url($this->headerList->get('referer'));
			}
			return $this->referrer;
		}

		public function isSecured(): bool {
			return $this->requestUrl->getScheme() === 'https';
		}

		public function isAjax(): bool {
			return $this->headerList->get('X-Requested-With') === 'XMLHttpRequest';
		}
	}
