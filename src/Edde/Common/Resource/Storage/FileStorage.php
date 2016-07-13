<?php
	namespace Edde\Common\Resource\Storage;

	use Edde\Api\Crate\CrateException;
	use Edde\Api\File\DirectoryException;
	use Edde\Api\File\FileException;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceIndex;
	use Edde\Api\Resource\ResourceException;
	use Edde\Api\Resource\Storage\IFileStorage;
	use Edde\Api\Resource\Storage\IStorageDirectory;
	use Edde\Api\Url\IUrl;
	use Edde\Common\File\Directory;
	use Edde\Common\File\FileUtils;
	use Edde\Common\Resource\FileResource;
	use Edde\Common\Usable\AbstractUsable;

	/**
	 * Simple and uniform way how to handle file storing.
	 */
	class FileStorage extends AbstractUsable implements IFileStorage {
		/**
		 * @var IResourceIndex
		 */
		protected $resourceIndex;
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
		 * @param IResourceIndex $resourceIndex
		 * @param IRootDirectory $rootDirectory
		 * @param IStorageDirectory $storageDirectory
		 */
		public function __construct(IResourceIndex $resourceIndex, IRootDirectory $rootDirectory, IStorageDirectory $storageDirectory) {
			$this->resourceIndex = $resourceIndex;
			$this->rootDirectory = $rootDirectory;
			$this->storageDirectory = $storageDirectory;
		}

		public function getPath(IResource $resource) {
			$resource = $this->getResource($resource);
			return str_replace($this->rootDirectory, null, $resource->getUrl()
				->getPath());
		}

		/**
		 * @param IResource $resource
		 *
		 * @return IResource
		 * @throws FileException
		 * @throws CrateException
		 * @throws ResourceException
		 */
		public function getResource(IResource $resource) {
			if ($this->hasResource($resource->getUrl()) === false) {
				return $this->store($resource);
			}
			return $this->resourceIndex->query()
				->name($resource->getUrl()
					->getAbsoluteUrl())
				->resource();
		}

		public function hasResource(IUrl $url) {
			$resourceQuery = $this->resourceIndex->query();
			$resourceQuery->name($url->getAbsoluteUrl());
			return $this->resourceIndex->hasResource($resourceQuery);
		}

		/**
		 * @param IResource $resource
		 *
		 * @return IResource
		 * @throws FileException
		 * @throws CrateException
		 * @throws ResourceException
		 */
		public function store(IResource $resource) {
			$this->usse();
			$url = $resource->getUrl();
			$directory = new Directory($this->storageDirectory->getDirectory() . '/' . sha1(dirname($url->getPath())));
			try {
				$directory->make();
			} catch (DirectoryException $e) {
				throw new ResourceException(sprintf('Cannot create store folder [%s] for the resource [%s].', $directory, $url), 0, $e);
			}
			FileUtils::copy($url, $file = $directory->getDirectory() . '/' . $url->getResourceName());
			$this->updateIndex($resource, $file);
			return new FileResource($file);
		}

		protected function updateIndex(IResource $resource, $file) {
			$url = $resource->getUrl();
			$resourceStorable = $this->resourceIndex->createResourceStorable();
			$resourceStorable->set('name', $url->getAbsoluteUrl());
			$resourceStorable->set('extension', $url->getExtension());
			$resourceStorable->set('url', (string)($file instanceof IUrl ? $file : (string)FileUtils::url($file)));
			$resourceStorable->set('mime', $resource->getMime());
			$this->resourceIndex->store($resourceStorable);
		}

		public function file($name, $content) {
			$this->usse();
			$this->updateIndex($resource = new FileResource($url = $this->storageDirectory->file($name, $content)
				->getUrl()), $url);
			return $resource;
		}

		public function hasFile($name) {
			return $this->resourceIndex->query()
				->nameLike('%' . $name)
				->hasResource();
		}

		public function getFile($name) {
			return $this->resourceIndex->query()
				->nameLike('%' . $name)
				->resource();
		}

		protected function prepare() {
			$this->storageDirectory->make();
			if (strpos($this->storageDirectory->getDirectory(), $this->rootDirectory->getDirectory()) === false) {
				throw new ResourceException(sprintf('Storage path [%s] is not in the given root [%s].', $this->storageDirectory, $this->rootDirectory));
			}
		}
	}
