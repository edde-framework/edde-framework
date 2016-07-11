<?php
	namespace Edde\Api\Schema;

	/**
	 * Definition of a schema property.
	 */
	interface IProperty {
		/**
		 * return schema to which this property belongs
		 *
		 * @return ISchema
		 */
		public function getSchema();

		/**
		 * return name of this property
		 *
		 * @return string
		 */
		public function getName();

		/**
		 * return full name of property, including schema and namespace
		 *
		 * @return string
		 */
		public function getPropertyName();

		/**
		 * is this property part of schema's identity?
		 *
		 * @return bool
		 */
		public function isIdentifier();

		/**
		 * @return string
		 */
		public function getType();

		/**
		 * is value of this property required?
		 *
		 * @return bool
		 */
		public function isRequired();

		/**
		 * has to be value of this property in it's schema unique?
		 *
		 * @return bool
		 */
		public function isUnique();

		/**
		 * is this property link to another property?
		 *
		 * @return bool
		 */
		public function isLink();

		/**
		 * return link of this property; throws exception if this property has no link
		 *
		 * @return ILink
		 */
		public function getLink();
	}
