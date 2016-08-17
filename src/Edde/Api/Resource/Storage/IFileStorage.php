<?php
	declare(strict_types = 1);

	namespace Edde\Api\Resource\Storage;

	use Edde\Api\Resource\IResource;

	interface IFileStorage {
		/**
		 * save the given resource to the file storage and return a new resource (local resource file)
		 *
		 * @param IResource $resource
		 *
		 * @return IResource
		 */
		public function store(IResource $resource);
	}
