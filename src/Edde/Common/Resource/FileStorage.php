<?php
	namespace Edde\Common\Resource;

	use Edde\Api\Crate\CrateException;
	use Edde\Api\Resource\IFileStorage;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceIndex;
	use Edde\Api\Resource\ResourceException;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Url\Url;
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
		 * @var string
		 */
		protected $root;
		/**
		 * storage dir; path to store incoming files
		 *
		 * @var string
		 */
		protected $storage;

		/**
		 * @param IResourceIndex $resourceIndex
		 * @param string $root
		 * @param string $storage
		 */
		public function __construct(IResourceIndex $resourceIndex, $root, $storage) {
			$this->resourceIndex = $resourceIndex;
			$this->root = $root;
			$this->storage = $storage;
		}

		public function getPath(IResource $resource) {
			$resource = $this->getResource($resource);
			return str_replace($this->root, null, $resource->getUrl()
				->getPath());
		}

		/**
		 * @param IResource $resource
		 *
		 * @return IResource
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
		 * @throws CrateException
		 * @throws ResourceException
		 */
		public function store(IResource $resource) {
			$this->usse();
			$url = $resource->getUrl();
			$path = $this->storage . '/' . sha1(dirname($url->getPath()));
			$file = $path . '/' . $url->getResourceName();
			if (@mkdir($path, 0777, true) && is_dir($path) === false) {
				throw new ResourceException(sprintf('Cannot create store folder [%s] for the resource [%s].', $path, $url));
			}
			copy($url, $file);
			$resourceStorable = $this->resourceIndex->createResourceStorable();
			$resourceStorable->set('name', $url->getAbsoluteUrl());
			$resourceStorable->set('extension', $url->getExtension());
			$resourceStorable->set('url', $localUrl = ('file:///' . str_replace('\\', '/', $file)));
			$resourceStorable->set('mime', $resource->getMime());
			$this->resourceIndex->store($resourceStorable);
			return new Resource(Url::create($localUrl));
		}

		protected function prepare() {
			$this->root = str_replace('\\', '/', $this->root);
			$this->storage = str_replace('\\', '/', $this->storage);
			if (is_dir($this->storage) === false && @mkdir($this->storage, 0777, true) && is_dir($this->storage) === false) {
				throw new ResourceException(sprintf('Cannot create file storage directory [%s].', $this->storage));
			}
			if (strpos($this->storage, $this->root) === false) {
				throw new ResourceException(sprintf('Storage path [%s] is not in the given root [%s].', $this->storage, $this->root));
			}
		}
	}
