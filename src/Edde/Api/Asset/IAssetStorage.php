<?php
	declare(strict_types = 1);

	namespace Edde\Api\Asset;

	use Edde\Api\Resource\IResource;
	use Edde\Api\Usable\IUsable;

	/**
	 * General storage for saving application data.
	 */
	interface IAssetStorage extends IUsable {
		/**
		 * save the given resource to the file storage and return a new resource (local resource file)
		 *
		 * @param IResource $resource
		 *
		 * @return IResource
		 */
		public function store(IResource $resource);
	}
