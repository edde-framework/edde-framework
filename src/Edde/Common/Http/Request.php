<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IRequest;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Url\Url;

	class Request extends AbstractHttp implements IRequest {
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
			$this->remoteHost === null && $this->remoteAddress !== null ? $this->remoteHost = gethostbyaddr($this->remoteAddress) : null;
		}

		public function getReferrer() {
			$this->referrer === null && $this->headerList->has('referer') ? $this->referrer = Url::create($this->headerList->get('referer')) : null;
		}

		public function isSecured(): bool {
			return $this->requestUrl->getScheme() === 'https';
		}

		public function isAjax(): bool {
			return $this->headerList->get('X-Requested-With') === 'XMLHttpRequest';
		}
	}
