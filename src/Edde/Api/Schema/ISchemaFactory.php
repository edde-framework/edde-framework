<?php
	declare(strict_types = 1);

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
		 * load the specific file as INode and add it to this factory
		 *
		 * @param string $file
		 *
		 * @return INode
		 */
		public function load(string $file): INode;

		/**
		 * create list of schemas based on a given schema nodes
		 *
		 * @return ISchema[]
		 */
		public function create();
	}
