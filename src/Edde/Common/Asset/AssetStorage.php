<?php
	declare(strict_types = 1);

	namespace Edde\Common\Asset;

	use Edde\Api\Asset\IAssetDirectory;
	use Edde\Api\Asset\IAssetStorage;
	use Edde\Api\File\DirectoryException;
	use Edde\Api\File\FileException;
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
		 * @var IAssetDirectory
		 */
		protected $assetDirectory;

		/**
		 * @param IRootDirectory $rootDirectory
		 * @param IAssetDirectory $assetDirectory
		 */
		public function __construct(IRootDirectory $rootDirectory, IAssetDirectory $assetDirectory) {
			$this->rootDirectory = $rootDirectory;
			$this->assetDirectory = $assetDirectory;
		}

		/**
		 * @inheritdoc
		 * @throws ResourceException
		 * @throws FileException
		 */
		public function store(IResource $resource) {
			$this->use();
			$url = $resource->getUrl();
			$directory = new Directory($this->assetDirectory->getDirectory() . '/' . sha1(dirname($url->getPath())));
			try {
				$directory->create();
			} catch (DirectoryException $e) {
				throw new ResourceException(sprintf('Cannot create store folder [%s] for the resource [%s].', $directory, $url), 0, $e);
			}
			FileUtils::copy($url->getAbsoluteUrl(), $file = $directory->filename($url->getResourceName()));
			return new File($file, dirname($this->assetDirectory->getDirectory()));
		}

		/**
		 * @inheritdoc
		 * @throws ResourceException
		 */
		protected function prepare() {
			$this->assetDirectory->create();
			if (strpos($this->assetDirectory->getDirectory(), $this->rootDirectory->getDirectory()) === false) {
				throw new ResourceException(sprintf('Storage path [%s] is not in the given root [%s].', $this->assetDirectory, $this->rootDirectory));
			}
		}
	}
