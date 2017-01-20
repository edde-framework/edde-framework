<?php
	declare(strict_types=1);

	namespace Edde\Api\Schema;

	use Edde\Api\Container\IConfigurable;
	use Edde\Api\Node\INode;

	/**
	 * Simple way how to load and build schemas from abstract source.
	 */
	interface ISchemaFactory extends IConfigurable {
		/**
		 * @param ISchemaProvider $schemaProvider
		 *
		 * @return ISchemaFactory
		 */
		public function registerSchemaProvider(ISchemaProvider $schemaProvider): ISchemaFactory;

		/**
		 * create schema from the input node
		 *
		 * @param INode $node
		 *
		 * @return ISchema
		 */
		public function createSchema(INode $node): ISchema;

		/**
		 * create list of schemas based on a given schema nodes
		 *
		 * @return ISchema[]
		 */
		public function create(): array;
	}
