<?php
	declare(strict_types=1);

	namespace Edde\Api\Http;
	use Edde\Api\Url\IUrl;

	/**
	 * Low level implementation of HTTP request.
	 */
	interface IRequest extends IHttp {
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
