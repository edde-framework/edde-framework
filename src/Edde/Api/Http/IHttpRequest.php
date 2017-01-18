<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	use Edde\Api\Url\IUrl;

	/**
	 * Interface describing http request; it can has arbitrary usage, not only for wrapping of
	 * PHP's $_REQUEST/... variables.
	 */
	interface IHttpRequest extends IHttp {
		/**
		 * @return string
		 */
		public function getMethod(): string;

		/**
		 * @param string $method
		 *
		 * @return bool
		 */
		public function isMethod(string $method): bool;

		/**
		 * @return null|string
		 */
		public function getRemoteAddress();

		/**
		 * @return null|string
		 */
		public function getRemoteHost();

		/**
		 * @return IUrl|null
		 */
		public function getReferrer();

		/**
		 * @return bool
		 */
		public function isSecured(): bool;

		/**
		 * @return bool
		 */
		public function isAjax(): bool;
	}
