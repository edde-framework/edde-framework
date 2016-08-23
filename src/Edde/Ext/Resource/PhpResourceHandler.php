<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Resource;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeUtils;
	use Edde\Common\Resource\AbstractResourceHandler;

	class PhpResourceHandler extends AbstractResourceHandler {
		public function getMimeTypeList(): array {
			return [
				'text/x-php',
			];
		}

		public function handle(IResource $resource, INode $root = null): INode {
			return (function ($url, INode $root) {
				NodeUtils::node($root, require($url));
				return $root;
			})((string)$resource->getUrl(), $root ?: new Node());
		}
	}
