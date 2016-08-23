<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Resource;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeUtils;
	use Edde\Common\Resource\AbstractResourceHandler;

	class JsonResourceHandler extends AbstractResourceHandler {
		public function getMimeTypeList(): array {
			return [
				'application/json',
			];
		}

		public function handle(IResource $resource, INode $root = null): INode {
			return NodeUtils::node($root ?: new Node(), json_decode($resource->get()));
		}
	}
