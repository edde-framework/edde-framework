<?php
	declare(strict_types = 1);

	namespace Edde;

	use Edde\Common\AbstractObject;

	/**
	 * Information about framework hidden in this class.
	 */
	class Framework extends AbstractObject {
		/**
		 * return full version string
		 *
		 * @return string
		 */
		public function getVersionString() {
			return $this->getVersion() . ' - ' . $this->getCodename();
		}

		/**
		 * return current version of framework
		 *
		 * @return string
		 */
		public function getVersion() {
			return '2.3.3.0';
		}

		/**
		 * return current codename of framework
		 *
		 * @return string
		 */
		public function getCodename() {
			return 'Red Fluffy Fox';
		}
	}
