<?php
	declare(strict_types = 1);

	namespace Edde\Api\Asset;

	use Edde\Api\Deffered\IDeffered;
	use Edde\Api\Resource\IResource;

	/**
	 * General storage for saving application data.
	 */
	interface IAssetStorage extends IDeffered {
		/**
		 * save the given resource to the file storage and return a new resource (local resource file)
		 *
		 * @param IResource $resource
		 *
		 * @return IResource
		 */
		public function store(IResource $resource);
	}
