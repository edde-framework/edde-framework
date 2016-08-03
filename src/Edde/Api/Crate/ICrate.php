<?php
	namespace Edde\Api\Crate;

	use Edde\Api\Schema\ISchema;
	use Edde\Api\Usable\IUsable;

	/**
	 * General object which is used to describe relations between objects (not necesarilly database objects) and
	 * theirs hierarchy.
	 */
	interface ICrate extends IUsable {
		/**
		 * schema can be set before crate is used (prepared)
		 *
		 * @param ISchema $schema
		 *
		 * @return $this
		 */
		public function setSchema(ISchema $schema);

		/**
		 * @return ISchema
		 */
		public function getSchema();

		/**
		 * @return IProperty[]
		 */
		public function getPropertyList();

		/**
		 * return list of identity values
		 *
		 * @return IProperty[]
		 */
		public function getIdentifierList();

		/**
		 * add the given value to this property set
		 *
		 * @param IProperty $property
		 * @param bool $force
		 *
		 * @return $this
		 */
		public function addProperty(IProperty $property, $force = false);

		/**
		 * has this property set property with the given name?
		 *
		 * @param string $name
		 *
		 * @return bool
		 */
		public function hasProperty($name);

		/**
		 * return value of the given name
		 *
		 * @param string $name
		 *
		 * @return IProperty
		 */
		public function getProperty($name);

		/**
		 * set value of the given property; if does not exists exception is thrown
		 *
		 * @param string $name
		 * @param mixed $value
		 *
		 * @return $this
		 *
		 * @throws CrateException
		 */
		public function set($name, $value);

		/**
		 * put (set) array of values to this crate; this can change state to dirty
		 *
		 * @param array $put
		 * @param bool $strict
		 *
		 * @return $this
		 */
		public function put(array $put, $strict = true);

		/**
		 * push array of values inside this property set; if strict is true, any unknown property will throw exception
		 *
		 * note: this will not make crate dirty
		 *
		 * @param array $push
		 * @param bool $strict
		 *
		 * @return $this
		 */
		public function push(array $push, $strict = true);

		/**
		 * return value of the given property; if property does not exist, exception is thrown
		 *
		 * @param string $name
		 * @param mixed|null $default
		 *
		 * @return mixed
		 */
		public function get($name, $default = null);

		/**
		 * has been any value in the property list changed?
		 *
		 * @return bool
		 */
		public function isDirty();

		/**
		 * return array of dirty values
		 *
		 * @return IProperty[]
		 */
		public function getDirtyList();

		/**
		 * return collection of the given name
		 *
		 * @param string $name
		 *
		 * @return ICollection|ICrate[]
		 */
		public function collection($name);

		/**
		 * explicitly set a new collection
		 *
		 * @param string $name
		 * @param ICollection $collection
		 *
		 * @return $this
		 */
		public function setCollection($name, ICollection $collection);

		/**
		 * return crate based on a predefined link
		 *
		 * @param string $name
		 *
		 * @return ICrate
		 */
		public function link($name);

		/**
		 * explicitly set a new link
		 *
		 * @param string $name
		 * @param ICrate $crate
		 *
		 * @return $this
		 */
		public function setLink($name, ICrate $crate);
	}
