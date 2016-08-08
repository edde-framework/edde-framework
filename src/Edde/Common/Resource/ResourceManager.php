<?php
	declare(strict_types = 1);

	namespace Edde\Common\Resource;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceHandler;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Resource\ResourceManagerException;
	use Edde\Common\Url\Url;
	use Edde\Common\Usable\AbstractUsable;

	class ResourceManager extends AbstractUsable implements IResourceManager {
		/**
		 * @var IResourceHandler[]
		 */
		protected $handlerList = [];

		public function registerResourceHandler(IResourceHandler $resourceHandler, bool $force = false): IResourceManager {
			foreach ($resourceHandler->getMimeTypeList() as $mime) {
				if (isset($this->handlerList[$mime]) && $force === false) {
					throw new ResourceManagerException(sprintf('Cannot register resource handler [%s]; mime type [%s] has been already registered by [%s].', get_class($resourceHandler), $mime, get_class($this->handlerList[$mime])));
				}
				$this->handlerList[$mime] = $resourceHandler;
			}
			return $this;
		}

		public function file(string $file, string $mime = null): INode {
			return $this->handle("file:///$file", $mime);
		}

		public function handle(string $url, string $mime = null): INode {
			return $this->getHandler($resource = new Resource(Url::create($url)), $mime)
				->handle($resource);
		}

		public function getHandler(IResource $resource, string $mime = null): IResourceHandler {
			$this->usse();
			if (isset($this->handlerList[$mime = $mime ?: $resource->getMime()]) === false) {
				throw new ResourceManagerException(sprintf('Requested unknown handler for a mime type [%s] of resource [%s].', $mime, (string)$resource->getUrl()));
			}
			return $this->handlerList[$mime];
		}

		protected function prepare() {
		}
	}
