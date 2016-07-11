<?php
	namespace Edde\Api\Schema;

	interface ISchema {
		/**
		 * return only the name of this schema without namespace
		 *
		 * @return string
		 */
		public function getName();

		/**
		 * return schema's namespace; this can be null
		 *
		 * @return string|null
		 */
		public function getNamespace();

		/**
		 * return full name of this schema (including namespace, ...)
		 *
		 * @return string
		 */
		public function getSchemaName();
	}
