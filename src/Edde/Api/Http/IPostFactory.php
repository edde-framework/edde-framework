<?php
	declare(strict_types = 1);

	namespace Edde\Api\Http;

	/**
	 * Implementation responsible for post list creation (url encoded data).
	 */
	interface IPostFactory {
		/**
		 * @return IPostList
		 */
		public function create(): IPostList;
	}
