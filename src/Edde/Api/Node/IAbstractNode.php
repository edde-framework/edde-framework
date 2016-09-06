<?php
	declare(strict_types = 1);

	namespace Edde\Api\Node;

	use Traversable;

	/**
	 * This is pure abstract interface for base (abstract) node implementation; it is not intended for direct usage.
	 */
	interface IAbstractNode {
		/**
		 * @param IAbstractNode $abstractNode
		 * @param bool $move change $node's parent to $this
		 *
		 * @return $this
		 */
		public function addNode(IAbstractNode $abstractNode, $move = false);

		/**
		 * push the given node into current node list; parent of $abstractNode is not changed
		 *
		 * @param IAbstractNode $abstractNode
		 *
		 * @return mixed
		 */
		public function pushNode(IAbstractNode $abstractNode);

		/**
		 * add list of given nodes to the current node
		 *
		 * @param Traversable|IAbstractNode[] $nodeList
		 * @param bool $move change parent of children to the current node
		 *
		 * @return $this
		 */
		public function addNodeList($nodeList, $move = false);

		/**
		 * replace current list of children by the given node list
		 *
		 * @param Traversable|IAbstractNode[] $nodeList
		 * @param bool|false $move
		 *
		 * @return $this
		 */
		public function setNodeList($nodeList, $move = false);

		/**
		 * move set of nodes to current node; if $move is true, parent of moved nodes is changed
		 *
		 * @param IAbstractNode $sourceNode children of this node will be moved
		 * @param bool|false $move === true, parent of children will changed to the current node
		 *
		 * @return $this
		 */
		public function moveNodeList(IAbstractNode $sourceNode, $move = false);

		/**
		 * remove the given node from the list of this node; if node is not found (by object comparsion), exception is thrown
		 *
		 * @param IAbstractNode $abstractNode
		 *
		 * @return $this
		 */
		public function removeNode(IAbstractNode $abstractNode);

		/**
		 * @return IAbstractNode[]
		 */
		public function getNodeList();

		/**
		 * @return $this
		 */
		public function clearNodeList();

		/**
		 * @param IAbstractNode $abstractNode
		 *
		 * @return $this
		 */
		public function setParent(IAbstractNode $abstractNode = null);

		/**
		 * @return IAbstractNode|null
		 */
		public function getParent();

		/**
		 * return list of parents in reverse order (from this node to the parents); break on the given parent if specified
		 *
		 * @param IAbstractNode $root
		 *
		 * @return IAbstractNode[]
		 */
		public function getParentList(IAbstractNode $root = null);

		/**
		 * @return IAbstractNode
		 */
		public function getRoot();

		/**
		 * @return IAbstractNode[]
		 */
		public function getAncestorList();

		/**
		 * @return bool
		 */
		public function isRoot();

		/**
		 * @return bool
		 */
		public function isChild();

		/**
		 * @return bool
		 */
		public function isLeaf();

		/**
		 * is this node last in the parent's node list? Throw an exception if this node is root
		 *
		 * @return bool
		 */
		public function isLast(): bool;

		/**
		 * @return int
		 */
		public function getLevel();

		/**
		 * @return int
		 */
		public function getTreeHeight();

		/**
		 * @return int
		 */
		public function getTreeSize();

		/**
		 * check, if this node can accept given node as child ({@see self::addNode()})
		 *
		 * @param IAbstractNode $abstractNode
		 *
		 * @return bool
		 */
		public function accept(IAbstractNode $abstractNode);

		/**
		 * return count of children
		 *
		 * @return int
		 */
		public function getNodeCount();

		/**
		 * insert the given node under current one (current one will have excatly one children)
		 *
		 * @param IAbstractNode $abstractNode
		 *
		 * @return IAbstractNode
		 */
		public function insert(IAbstractNode $abstractNode): IAbstractNode;

		/**
		 * @param IAbstractNode $abstractNode
		 *
		 * @return IAbstractNode return newly switched node
		 */
		public function switch (IAbstractNode $abstractNode): IAbstractNode;

		/**
		 * replace the given child node by the list of nodes
		 *
		 * @param IAbstractNode $abstractNode
		 * @param array $nodeList
		 *
		 * @return IAbstractNode
		 */
		public function replaceNode(IAbstractNode $abstractNode, array $nodeList): IAbstractNode;
	}
