<?php
	declare(strict_types=1);

	namespace Edde\Common\Node;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\Object;
	use Edde\Common\Strings\StringException;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Set of tools for work with nodes.
	 */
	class NodeUtils extends Object {
		/**
		 * @param INode                        $root
		 * @param \Traversable|\Iterator|array $source
		 *
		 * @return INode
		 * @throws NodeException
		 */
		static public function node(INode $root, $source): INode {
			$callback = null;
			if (is_array($source) === false && is_object($source) === false) {
				throw new NodeException('Source must be array or stdClass object.');
			}
			/** @noinspection UnnecessaryParenthesesInspection */
			return ($callback = function (callable $callback, INode $root, $source) {
				$attributeList = $root->getAttributeList();
				/** @noinspection ForeachSourceInspection */
				foreach ($source as $key => $value) {
					switch ($key) {
						case 'name':
							$root->setName($value);
							continue 2;
						case 'value':
							$root->setValue($value);
							continue 2;
						case 'attribute-list':
							$attributeList->put((array)$value);
							continue 2;
						case 'meta-list':
							$root->getMetaList()->put((array)$value);
							continue 2;
						case 'node-list':
							/** @noinspection ForeachSourceInspection */
							foreach ($value as $item) {
								/** @noinspection DisconnectedForeachInstructionInspection */
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
							/** @noinspection DisconnectedForeachInstructionInspection */
							$itemList->addNode($node = new Node());
							if (is_object($item) || is_array($item)) {
								$callback($callback, $node, $item);
								continue;
							}
							$node->setValue($item);
						}
						continue;
					}
					$attributeList->set($key, $value);
				}
				return $root;
			})($callback, $root, $source);
		}

		/**
		 * convert input of stdClass to node tree
		 *
		 * @param \stdClass $stdClass
		 * @param INode     $node
		 *
		 * @return INode
		 * @throws NodeException
		 */
		static public function convert(\stdClass $stdClass, INode $node = null): INode {
			$node = $node ?: new Node();

			foreach ($stdClass as $k => $v) {
				if ($k === 'name') {
					$node->setName($v);
					continue;
				} else if ($v instanceof \stdClass) {
					$node->addNode(self::convert($v, new Node($k)));
					continue;
				} else if (is_array($v)) {
					$node->addNode($root = new Node($k));
					foreach ($v as $vv) {
						$root->addNode(self::convert($vv, new Node()));
					}
					continue;
				}
				$node->setAttribute($k, $v);
			}
			return $node;
		}

		/**
		 * namespecize the given node tree; attributes matching the given preg will be converted to namespace structure
		 *
		 * @param INode  $root
		 * @param string $preg
		 *
		 * @throws NodeException
		 * @throws StringException
		 */
		static public function namespace(INode $root, string $preg) {
			foreach (NodeIterator::recursive($root, true) as $node) {
				$attributeList = $node->getAttributeList();
				foreach ($attributeList as $k => $value) {
					if (($match = StringUtils::match($k, $preg, true)) !== null) {
						$attributeList->set($match['namespace'], $namespace = $attributeList->get($match['namespace'], new AttributeList()));
						$namespace->set($match['name'], $value);
						$attributeList->remove($k);
					}
				}
			}
		}
	}
