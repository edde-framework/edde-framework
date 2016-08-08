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

		public function handle(IResource $resource): INode {
			return $this->node($node = new Node(), json_decode($resource->get()));
		}

		protected function node(INode $root, $source) {
			foreach ($source as $key => $value) {
				switch ($key) {
					case 'name':
						$root->setName($value);
						break;
					case 'value':
						$root->setValue($value);
						break;
					case 'attribute-list':
						$root->setAttributeList((array)$value);
						break;
					case 'meta-list':
						$root->setMetaList((array)$value);
						break;
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
