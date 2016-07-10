<?php
	namespace Edde\Api\Resource;

	use Edde\Api\Url\IUrl;

	/**
	 * General interface describing resource "somewhere"; it can be file, url, any resource.
	 */
	interface IResource {
		/**
		 * return resource's location; it can even be on local filesystem
		 *
		 * @return IUrl
		 */
		public function getUrl();
	}
