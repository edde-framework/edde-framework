<?php
	namespace Edde\Api\Schema;

	interface ISchema {
		/**
		 * return full name of this schema (including namespace, ...)
		 *
		 * @return string
		 */
		public function getSchemaName();

		/**
		 * return only the name of this schema without namespace
		 *
		 * @return string
		 */
		public function getName();
	}
