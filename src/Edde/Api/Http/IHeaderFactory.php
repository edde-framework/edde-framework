<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	/**
	 * Formal interface for accessing request headers.
	 */
	interface IHeaderFactory {
		/**
		 * @return IHeaderList
		 */
		public function create(): IHeaderList;
	}
