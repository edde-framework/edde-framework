<?php
	namespace Edde\Common\Node;

	use ArrayIterator;
	use Edde\Api\Node\IAbstractNode;
	use Edde\Common\AbstractObject;
	use RecursiveIterator;
	use RecursiveIteratorIterator;

	class NodeIterator extends AbstractObject implements RecursiveIterator {
		/**
		 * @var IAbstractNode
		 */
		private $node;
		/**
		 * @var \Iterator
		 */
		private $iterator;

		public function __construct(IAbstractNode $node) {
			$this->node = $node;
		}

		static public function create(IAbstractNode $abstractNode) {
			return new self($abstractNode);
		}

		static public function recursive(IAbstractNode $abstractNode, $root = false) {
			if ($root === true) {
				$root = new Node();
				$root->pushNode($abstractNode);
				$abstractNode = $root;
			}
			return new RecursiveIteratorIterator(new self($abstractNode), RecursiveIteratorIterator::SELF_FIRST);
		}

		public function next() {
			$this->iterator->next();
		}

		public function key() {
			return $this->iterator->key();
		}

		public function valid() {
			return $this->iterator->valid();
		}

		public function rewind() {
			$this->iterator = new ArrayIterator($this->node->getNodeList());
			$this->iterator->rewind();
		}

		public function hasChildren() {
			$current = $this->current();
			return $current->isLeaf() === false;
		}

		public function current() {
			return $this->iterator->current();
		}

		public function getChildren() {
			return new self($this->current());
		}
	}
