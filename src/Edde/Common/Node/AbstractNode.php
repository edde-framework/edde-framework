<?php
	declare(strict_types = 1);

	namespace Edde\Common\Node;

	use Edde\Api\Node\IAbstractNode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\AbstractObject;

	/**
	 * Pure node tree implementation; this class holds all common methods for node manipulation.
	 */
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

		/**
		 * State-of-the-art: any computer you can't afford.
		 *
		 * @param IAbstractNode|null $parent
		 */
		public function __construct(IAbstractNode $parent = null) {
			$this->parent = $parent;
		}

		/**
		 * @inheritdoc
		 */
		public function getRoot() {
			$parent = $this;
			foreach ($this->getParentList() as $parent) {
				;
			}
			return $parent;
		}

		/**
		 * @inheritdoc
		 */
		public function getParentList(IAbstractNode $root = null) {
			$parent = $this->getParent();
			$parentList[] = $root ?: $parent;
			while ($parent && $parent !== $root) {
				$parentList[] = $parent;
				$parent = $parent->getParent();
			}
			return $parentList;
		}

		/**
		 * @inheritdoc
		 */
		public function getParent() {
			return $this->parent;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function setParent(IAbstractNode $abstractNode = null) {
			if ($abstractNode !== null && $abstractNode->accept($this) === false) {
				throw new NodeException(sprintf("Cannot set parent for [%s]: parent [%s] doesn't accept this node.", static::class, get_class($abstractNode)));
			}
			$this->parent = $abstractNode;
			$this->level = null;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isChild() {
			return $this->getParent() !== null;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function addNodeList($nodeList, $move = false) {
			foreach ($nodeList as $node) {
				$this->addNode($node, $move);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function addNode(IAbstractNode $abstractNode, $move = false) {
			if ($this->accept($abstractNode) === false) {
				throw new NodeException(sprintf("Current node [%s] doesn't accept given node [%s].", static::class, get_class($abstractNode)));
			}
			/** @var $parent IAbstractNode */
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

		/**
		 * @inheritdoc
		 */
		public function prepend(IAbstractNode $abstractNode): IAbstractNode {
			$this->nodeList = array_merge([$abstractNode], $this->nodeList);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function pushNode(IAbstractNode $abstractNode) {
			if ($this->accept($abstractNode) === false) {
				throw new NodeException(sprintf("Current node [%s] doesn't accept given node [%s].", static::class, get_class($abstractNode)));
			}
			$this->nodeList[] = $abstractNode;
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function moveNodeList(IAbstractNode $sourceNode, $move = false) {
			foreach ($sourceNode->getNodeList() as $node) {
				$sourceNode->removeNode($node);
				$this->addNode($node, $move);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
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

		/**
		 * @inheritdoc
		 */
		public function clearNodeList() {
			foreach ($this->nodeList as $node) {
				$node->setParent(null);
			}
			$this->nodeList = [];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getAncestorList() {
			$ancestorList = [];
			$node = $this;
			while ($parent = $node->getParent()) {
				array_unshift($ancestorList, $parent);
				$node = $parent;
			}
			return $ancestorList;
		}

		/**
		 * @inheritdoc
		 */
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

		/**
		 * @inheritdoc
		 */
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

		/**
		 * @inheritdoc
		 */
		public function isLeaf() {
			return count($this->nodeList) === 0;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function isLast(): bool {
			if ($this->isRoot()) {
				throw new NodeException(sprintf('Cannot check last flag of root node.'));
			}
			$nodeList = $this->getParent()
				->getNodeList();
			return end($nodeList) === $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isRoot() {
			return $this->getParent() === null;
		}

		/**
		 * @inheritdoc
		 */
		public function getTreeSize() {
			$size = 1;
			foreach ($this->nodeList as $node) {
				$size += $node->getTreeSize();
			}
			return $size;
		}

		/**
		 * @inheritdoc
		 */
		public function getNodeCount() {
			return count($this->nodeList);
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function insert(IAbstractNode $abstractNode): IAbstractNode {
			if ($abstractNode->isLeaf() === false) {
				throw new NodeException('Node must be empty.');
			}
			$this->addNode($abstractNode->addNodeList($this->getNodeList(), true));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getNodeList() {
			return $this->nodeList;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function setNodeList($nodeList, $move = false) {
			$this->nodeList = [];
			foreach ($nodeList as $node) {
				$this->addNode($node, $move);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function switch (IAbstractNode $abstractNode): IAbstractNode {
			if (($parent = $this->getParent()) !== null) {
				$parent->replaceNode($this, [$abstractNode]);
			}
			$abstractNode->addNode($this);
			$abstractNode->setParent($parent);
			$this->setParent($abstractNode);
			return $abstractNode;
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function replaceNode(IAbstractNode $abstractNode, array $nodeList): IAbstractNode {
			if (($index = array_search($abstractNode, $this->nodeList, true)) === false || $abstractNode->getParent() !== $this) {
				throw new NodeException(sprintf('Cannot replace the given node in root; root is not parent of the given node.'));
			}
			array_splice($this->nodeList, $index, 0, $nodeList);
			unset($this->nodeList[array_search($abstractNode, $this->nodeList, true)]);
			foreach ($nodeList as $node) {
				$node->setParent($this);
			}
			return $this;
		}

		public function __clone() {
			foreach ($this->nodeList as &$node) {
				$node = clone $node;
			}
			unset($node);
		}
	}
