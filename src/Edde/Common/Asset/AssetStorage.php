<?php
	declare(strict_types = 1);

	namespace Edde\Common\Asset;

	use Edde\Api\Asset\IAssetStorage;
	use Edde\Api\Asset\IStorageDirectory;
	use Edde\Api\File\DirectoryException;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\ResourceException;
	use Edde\Common\File\Directory;
	use Edde\Common\File\File;
	use Edde\Common\File\FileUtils;
	use Edde\Common\Usable\AbstractUsable;

	/**
	 * Simple and uniform way how to handle file storing.
	 */
	class AssetStorage extends AbstractUsable implements IAssetStorage {
		/**
		 * application root directory; it is used for relative path computation
		 *
		 * @var IRootDirectory
		 */
		protected $rootDirectory;
		/**
		 * storage dir; path to store incoming files
		 *
		 * @var IStorageDirectory
		 */
		protected $storageDirectory;

		/**
		 * @param IRootDirectory $rootDirectory
		 * @param IStorageDirectory $storageDirectory
		 */
		public function __construct(IRootDirectory $rootDirectory, IStorageDirectory $storageDirectory) {
			$this->rootDirectory = $rootDirectory;
			$this->storageDirectory = $storageDirectory;
		}

		public function store(IResource $resource) {
			$this->use();
			$url = $resource->getUrl();
			$directory = new Directory($this->storageDirectory->getDirectory() . '/' . sha1(dirname($url->getPath())));
			try {
				$directory->create();
			} catch (DirectoryException $e) {
				throw new ResourceException(sprintf('Cannot create store folder [%s] for the resource [%s].', $directory, $url), 0, $e);
			}
			FileUtils::copy($url->getAbsoluteUrl(), $file = $directory->filename($url->getResourceName()));
			return new File($file, dirname($this->storageDirectory->getDirectory()));
		}

		protected function prepare() {
			$this->storageDirectory->create();
			if (strpos($this->storageDirectory->getDirectory(), $this->rootDirectory->getDirectory()) === false) {
				throw new ResourceException(sprintf('Storage path [%s] is not in the given root [%s].', $this->storageDirectory, $this->rootDirectory));
			}
		}
	}
