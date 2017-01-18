<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	/**
	 * Everything is broken to pieces.
	 */
	interface IRequestUrlFactory {
		/**
		 * @return IRequestUrl
		 */
		public function createRequestUrl(): IRequestUrl;
	}
