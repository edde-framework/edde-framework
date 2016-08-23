<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Resource;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Common\Node\Node;
	use Edde\Common\Resource\AbstractResourceHandler;

	class JsonResourceHandler extends AbstractResourceHandler {
		public function getMimeTypeList(): array {
			return [
				'application/json',
			];
		}

		public function handle(IResource $resource, INode $root = null): INode {
			return $this->node($root ?: new Node(), json_decode($resource->get()));
		}

		protected function node(INode $root, $source) {
			foreach ($source as $key => $value) {
				switch ($key) {
					case 'name':
						$root->setName($value);
						continue 2;
					case 'value':
						$root->setValue($value);
						continue 2;
					case 'attribute-list':
						$root->addAttributeList((array)$value);
						continue 2;
					case 'meta-list':
						$root->addMetaList((array)$value);
						continue 2;
				}
				if (is_object($value)) {
					$value = [
						$value,
					];
				}
				if (is_array($value)) {
					$root->addNode($itemList = new Node($key));
					foreach ($value as $item) {
						$this->node($n = new Node(), $item);
						$itemList->addNode($n);
					}
					continue;
				}
				$root->setAttribute($key, $value);
			}
			return $root;
		}
	}
