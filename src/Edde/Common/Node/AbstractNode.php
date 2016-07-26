<?php
	namespace Edde\Common\Node;

	use Edde\Api\Node\IAbstractNode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\AbstractObject;

	abstract class AbstractNode extends AbstractObject implements IAbstractNode {
		/**
		 * @var IAbstractNode
		 */
		protected $parent;
		/**
		 * @var IAbstractNode[]
		 */
		protected $nodeList = [];
		/**
		 * @var int
		 */
		protected $level;

		protected function __construct(IAbstractNode $parent = null) {
			$this->parent = $parent;
		}

		public function getRoot() {
			$parent = $this;
			foreach ($this->getParentList() as $parent) {
				;
			}
			return $parent;
		}

		public function getParentList(IAbstractNode $root = null) {
			$parent = $this->getParent();
			$parentList[] = $root ?: $parent;
			while ($parent && $parent !== $root) {
				$parentList[] = $parent;
				$parent = $parent->getParent();
			}
			return $parentList;
		}

		public function getParent() {
			return $this->parent;
		}

		public function setParent(IAbstractNode $abstractNode = null) {
			if ($abstractNode !== null && $abstractNode->accept($this) === false) {
				throw new NodeException(sprintf("Cannot set parent for [%s]: parent [%s] doesn't accept this node.", static::class, get_class($abstractNode)));
			}
			$this->parent = $abstractNode;
			$this->level = null;
			return $this;
		}

		public function isRoot() {
			return $this->getParent() === null;
		}

		public function isChild() {
			return $this->getParent() !== null;
		}

		public function addNodeList($nodeList, $move = false) {
			foreach ($nodeList as $node) {
				$this->addNode($node, $move);
			}
			return $this;
		}

		public function addNode(IAbstractNode $abstractNode, $move = false) {
			if ($this->accept($abstractNode) === false) {
				throw new NodeException(sprintf("Current node [%s] doesn't accept given node [%s].", static::class, get_class($abstractNode)));
			}
			$parent = $abstractNode->getParent();
			if ($move || $parent === null) {
				if ($parent) {
					$parent->removeNode($abstractNode);
				}
				$abstractNode->setParent($this);
			}
			$this->nodeList[] = $abstractNode;
			return $this;
		}

		public function pushNode(IAbstractNode $abstractNode) {
			if ($this->accept($abstractNode) === false) {
				throw new NodeException(sprintf("Current node [%s] doesn't accept given node [%s].", static::class, get_class($abstractNode)));
			}
			$this->nodeList[] = $abstractNode;
			return $this;
		}

		public function moveNodeList(IAbstractNode $sourceNode, $move = false) {
			foreach ($sourceNode->getNodeList() as $node) {
				$sourceNode->removeNode($node);
				$this->addNode($node, $move);
			}
			return $this;
		}

		public function removeNode(IAbstractNode $abstractNode) {
			foreach ($this->nodeList as $index => $node) {
				if ($node === $abstractNode) {
					$node->setParent(null);
					unset($this->nodeList[$index]);
					return;
				}
			}
			throw new NodeException('The given node is not in current node list.');
		}

		public function getNodeList() {
			return $this->nodeList;
		}

		public function setNodeList($nodeList, $move = false) {
			$this->nodeList = [];
			foreach ($nodeList as $node) {
				$this->addNode($node, $move);
			}
			return $this;
		}

		public function clearNodeList() {
			foreach ($this->nodeList as $node) {
				$node->setParent(null);
			}
			$this->nodeList = [];
			return $this;
		}

		public function getAncestorList() {
			$ancestorList = [];
			$node = $this;
			while ($parent = $node->getParent()) {
				array_unshift($ancestorList, $parent);
				$node = $parent;
			}
			return $ancestorList;
		}

		public function getLevel() {
			if ($this->level !== null) {
				return $this->level;
			}
			$this->level = 0;
			$node = $this;
			while ($parent = $node->getParent()) {
				$this->level++;
				$node = $parent;
			}
			return $this->level;
		}

		public function getTreeHeight() {
			if ($this->isLeaf()) {
				return 0;
			}
			$heightList = [];
			foreach ($this->nodeList as $node) {
				$heightList[] = $node->getTreeHeight();
			}
			return max($heightList) + 1;
		}

		public function isLeaf() {
			return count($this->nodeList) === 0;
		}

		public function getTreeSize() {
			$size = 1;
			foreach ($this->nodeList as $node) {
				$size += $node->getTreeSize();
			}
			return $size;
		}

		public function getNodeCount() {
			return count($this->nodeList);
		}

		public function __clone() {
			throw new NodeException(sprintf('Clone is not supported on the [%s].', static::class));
		}
	}
