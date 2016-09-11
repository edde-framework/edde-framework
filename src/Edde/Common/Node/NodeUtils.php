<?php
	declare(strict_types = 1);

	namespace Edde\Common\Node;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\AbstractObject;

	class NodeUtils extends AbstractObject {
		static public function node(INode $root, $source) {
			$callback = null;
			if (is_array($source) === false && is_object($source) === false) {
				throw new NodeException('Source must be array or stdClass object.');
			}
			return ($callback = function (callable $callback, INode $root, $source) {
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
						case 'node-list':
							foreach ($value as $item) {
								$root->addNode($node = new Node());
								if (is_object($item) || is_array($item)) {
									$callback($callback, $node, $item);
									continue;
								}
								$node->setValue($item);
							}
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
							$itemList->addNode($node = new Node());
							if (is_object($item) || is_array($item)) {
								$callback($callback, $node, $item);
								continue;
							}
							$node->setValue($item);
						}
						continue;
					}
					$root->setAttribute($key, $value);
				}
				return $root;
			})($callback, $root, $source);
		}
	}
