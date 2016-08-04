<?php
	namespace Edde\Api\Schema;

	/**
	 * General way how to handle schemas.
	 */
	interface ISchemaManager {
		/**
		 * register a new schema to this manager; if there is schema with schema name, it is silently replaced
		 *
		 * @param ISchema $schema
		 *
		 * @return $this
		 */
		public function addSchema(ISchema $schema);

		/**
		 * is there schema with this name? - name must be full schema name including namespace
		 *
		 * @param string $schema
		 *
		 * @return bool
		 */
		public function hasSchema($schema);

		/**
		 * return schema by name; name must be full schema name
		 *
		 * @param string $schema
		 *
		 * @return ISchema
		 *
		 * @throws SchemaException
		 */
		public function getSchema($schema);

		/**
		 * return list of all available schemas
		 *
		 * @return ISchema[]
		 */
		public function getSchemaList();
	}
