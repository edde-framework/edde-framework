<?php
	declare(strict_types = 1);

	namespace Edde\Common\Resource;

	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Resource\ResourceManagerException;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\File\File;
	use Edde\Common\Url\Url;

	class ResourceManager extends AbstractDeffered implements IResourceManager {
		use LazyConverterManagerTrait;

		public function file(string $file, string $mime = null, INode $root = null): INode {
			return $this->resource(new File($file), $mime, $root);
		}

		public function resource(IResource $resource, string $mime = null, INode $root = null): INode {
			$mime = $mime ?: $resource->getMime();
			/** @var $node INode */
			if (($node = $this->converterManager->convert($resource, $mime, INode::class)) instanceof INode === false) {
				throw new ResourceManagerException(sprintf('Convertion has failed: converter for [%s] did not returned an instance of [%s].', $mime, INode::class));
			}
			if ($root) {
				$root->setNodeList($node->getNodeList(), true);
			}
			return $root ?? $node;
		}

		public function handle(string $url, string $mime = null, INode $root = null): INode {
			return $this->resource($resource = new Resource(Url::create($url)), $mime, $root);
		}

		protected function prepare() {
		}
	}
