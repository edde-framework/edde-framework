<?php
	namespace Edde\Api\Resource\Storage;

	use Edde\Api\Resource\IResource;
	use Edde\Api\Url\IUrl;

	interface IFileStorage {
		/**
		 * @param IResource $resource
		 *
		 * @return $this
		 */
		public function store(IResource $resource);

		/**
		 * save the content of the given name to this storage and return a new IResource
		 *
		 * @param string $name
		 * @param string $content
		 *
		 * @return IResource
		 */
		public function file($name, $content);

		/**
		 * check if the given url is known in the file storage; requested URL shu7ld be external resource
		 *
		 * @param IUrl $url
		 *
		 * @return bool
		 */
		public function hasResource(IUrl $url);

		/**
		 * retrieve resource by the original resource (it's URL is used)
		 *
		 * @param IResource $resource
		 *
		 * @return IResource
		 */
		public function getResource(IResource $resource);

		/**
		 * query for a relative path of the given resource (for example this is useful for moving files to a public directory of a web server)
		 *
		 * @param IResource $resource
		 *
		 * @return string
		 */
		public function getPath(IResource $resource);
	}
