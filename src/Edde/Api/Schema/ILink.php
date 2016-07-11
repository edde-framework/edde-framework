<?php
	namespace Edde\Api\Schema;

	/**
	 * Defines relation between properties; every property which is link must point to another
	 * property.
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
	}
