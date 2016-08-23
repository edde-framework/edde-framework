<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Resource;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Common\Resource\AbstractResourceHandler;

	class PhpResourceHandler extends AbstractResourceHandler {
		public function getMimeTypeList(): array {
			return [
				'text/x-php',
			];
		}

		public function handle(IResource $resource, INode $root = null): INode {
		}
	}
