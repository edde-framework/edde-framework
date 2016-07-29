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

		/**
		 * return set of properties of this Schema
		 *
		 * @return IProperty[]
		 */
		public function getPropertyList();

		/**
		 * tells if given property name is known in this schema
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasProperty($name);

		/**
		 * retrieve the given property; throws exception if the property is not known for this schema
		 *
		 * @param string $name
		 *
		 * @return IProperty
		 *
		 * @throws SchemaException
		 */
		public function getProperty($name);

		/**
		 * register link to a schema; if the link name is present, exception should be thrown
		 *
		 * @param ILink $link
		 * @param bool $force === true add a new link regardless of it's presence
		 *
		 * @return $this
		 */
		public function addLink(ILink $link, $force = false);

		/**
		 * is there link with the given name?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasLink($name);

		/**
		 * return a link with the given name
		 *
		 * @param string $name
		 *
		 * @return ILink
		 */
		public function getLink($name);

		/**
		 * return all known links in this schema
		 *
		 * @return ILink[]
		 */
		public function getLinkList();
	}
