<?php
	declare(strict_types=1);

	namespace Edde\Api\Node;

	/**
	 * INode is extended version of IAbstractNode which holds name, value, attributes and metadata; it can be used as
	 * complex (tree) data structure holder. It is similar to XML node.
	 */
	interface INode extends IAbstractNode {
		/**
		 * @param string $name
		 *
		 * @return $this
		 */
		public function setName(string $name);

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @param mixed $value
		 *
		 * @return $this
		 */
		public function setValue($value);

		/**
		 * @param mixed|null $default
		 *
		 * @return mixed
		 */
		public function getValue($default = null);

		/**
		 * return list of attributes for this node
		 *
		 * @return IAttributeList
		 */
		public function getAttributeList(): IAttributeList;

		/**
		 * return list of meta data
		 *
		 * @return IAttributeList
		 */
		public function getMetaList(): IAttributeList;

		/**
		 * generate materialized path from node names
		 *
		 * @return INode|null
		 */
		public function getParent();

		/**
		 * @param bool $attributes include attribute names (e.g. [foo][bar], ....)
		 * @param bool $meta       include meta names (e.g. (foo)(bar)
		 *
		 * @return string
		 */
		public function getPath($attributes = false, $meta = false);

		/**
		 * @return INode[]
		 */
		public function getNodeList(): array;
	}
