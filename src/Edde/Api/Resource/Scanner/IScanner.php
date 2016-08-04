<?php
	declare(strict_types = 1);

	namespace Edde\Api\Resource\Scanner;

	use Edde\Api\Resource\IResource;

	/**
	 * Scanners are used for searching for a resources and populating (storage, depending on a implementation).
	 */
	interface IScanner {
		/**
		 * return iterator over IResource
		 *
		 * @return \Iterator|IResource[]
		 */
		public function scan();
	}
