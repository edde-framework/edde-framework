<?php
	namespace Edde\Api\Schema;

	use Edde\Api\Usable\IUsable;

	interface ISchema extends IUsable {
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
		 * @return ISchemaProperty
		 *
		 * @throws SchemaException
		 */
		public function getProperty($name);

		/**
		 * return set of properties of this Schema
		 *
		 * @return ISchemaProperty[]
		 */
		public function getPropertyList();

		/**
		 * make a link between the given source and destination property (1:1); if the name is present and force === false, exception should be thrown
		 *
		 * @param string $name
		 * @param ISchemaProperty $source
		 * @param ISchemaProperty $target
		 * @param bool $force
		 *
		 * @return $this
		 */
		public function link($name, ISchemaProperty $source, ISchemaProperty $target, $force = false);

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
		 * @return ISchemaLink
		 */
		public function getLink($name);

		/**
		 * return all known links in this schema
		 *
		 * @return ISchemaLink[]
		 */
		public function getLinkList();

		/**
		 * connect the given source property to the target property as 1:n collection
		 *
		 * @param string $name
		 * @param ISchemaProperty $source
		 * @param ISchemaProperty $target
		 * @param bool $force
		 *
		 * @return $this
		 */
		public function collection($name, ISchemaProperty $source, ISchemaProperty $target, $force = false);

		/**
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasCollection($name);

		/**
		 * @param string $name
		 *
		 * @return ISchemaCollection
		 */
		public function getCollection($name);

		/**
		 * @return ISchemaCollection[]
		 */
		public function getCollectionList();

		/**
		 * link the given source property to the given target property in both directions (link + collection in reverse); this is only shorthand for link(source, target) + collection(target, source)
		 *
		 * @param string $link
		 * @param string $collection
		 * @param ISchemaProperty $source
		 * @param ISchemaProperty $target
		 *
		 * @return $this
		 */
		public function linkTo($link, $collection, ISchemaProperty $source, ISchemaProperty $target);
	}
