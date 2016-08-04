<?php
	namespace Edde\Api\Schema;

	use Edde\Api\Node\INode;
	use Edde\Api\Usable\IUsable;

	/**
	 * Simple way how to load and build schemas from abstract source.
	 */
	interface ISchemaFactory extends IUsable {
		/**
		 * add a schema node
		 *
		 * @param INode $node
		 *
		 * @return $this
		 */
		public function addSchemaNode(INode $node);

		/**
		 * create list of schemas based on a given schema nodes
		 *
		 * @return ISchema[]
		 */
		public function create();
	}
