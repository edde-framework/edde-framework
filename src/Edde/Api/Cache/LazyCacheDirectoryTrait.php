<?php
	declare(strict_types=1);

	namespace Edde\Api\Cache;

	trait LazyCacheDirectoryTrait {
		/**
		 * @cache-optional
		 * @var ICacheDirectory
		 */
		protected $cacheDirectory;

		/**
		 * @param ICacheDirectory $cacheDirectory
		 */
		public function lazyCacheDirectory(ICacheDirectory $cacheDirectory) {
			$this->cacheDirectory = $cacheDirectory;
		}
	}
