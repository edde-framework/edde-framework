<?php
	declare(strict_types=1);

	namespace Edde\Common\Resource;

	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\File\FileException;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Resource\ResourceManagerException;
	use Edde\Common\File\File;
	use Edde\Common\Object;
	use Edde\Common\Url\Url;

	/**
	 * Default implementation of a resource manager.
	 */
	class ResourceManager extends Object implements IResourceManager {
		use LazyConverterManagerTrait;

		/**
		 * @inheritdoc
		 * @throws FileException
		 * @throws ResourceManagerException
		 */
		public function file(string $file, string $mime = null, INode $root = null): INode {
			return $this->resource(new File($file), $mime, $root);
		}

		/**
		 * @inheritdoc
		 * @throws ResourceManagerException
		 */
		public function resource(IResource $resource, string $mime = null, INode $root = null): INode {
			$mime = $mime ?: $resource->getMime();
			$this->converterManager->setup();
			/** @var $node INode */
			$convertable = $this->converterManager->convert($resource, $mime, [INode::class]);
			if (($node = $convertable->convert()) instanceof INode === false) {
				throw new ResourceManagerException(sprintf('Conversion has failed: converter for [%s] did not returned an instance of [%s].', $mime, INode::class));
			}
			if ($root) {
				$root->setNodeList($node->getNodeList(), true);
			}
			return $root ?? $node;
		}

		/**
		 * @inheritdoc
		 * @throws ResourceManagerException
		 */
		public function handle(string $url, string $mime = null, INode $root = null): INode {
			return $this->resource($resource = new Resource(Url::create($url)), $mime, $root);
		}
	}
