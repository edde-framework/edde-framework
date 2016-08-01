<?php
	namespace Edde\Api\Schema;

	/**
	 * Defines relation between properties; every property which is link must point to another
	 * property.
	 *
	 * ILink can represent only two kind of links:
	 * 1:1 link (for example row bound to the header); isSingleLink() === true; isMultiLink() === false
	 * 1:n link (for example header with many rows); isSingleLink() === false; isMultiLink() === true
	 */
	interface ILink {
		/**
		 * link name
		 *
		 * @return string
		 */
		public function getName();

		/**
		 * initial property
		 *
		 * @return IProperty
		 */
		public function getSource();

		/**
		 * target property
		 *
		 * @return IProperty
		 */
		public function getTarget();

		/**
		 * the 1:1 kind of link (one source to one target); this is only formal definition of link, not physical representation
		 *
		 * @return bool
		 */
		public function isSingleLink();

		/**
		 * the 1:n kind of link (on source to many targets); this is only formal definition of link, not physical representation
		 *
		 * @return bool
		 */
		public function isMultiLink();
	}
