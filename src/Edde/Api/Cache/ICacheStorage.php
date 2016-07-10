<?php
	namespace Edde\Api\Cache;

	use Edde\Api\Usable\IUsable;

	interface ICacheStorage extends IUsable {
		/**
		 * @param string $id
		 * @param mixed $save must be serializable
		 *
		 * @return mixed returns input $save
		 */
		public function save($id, $save);

		/**
		 * @param string $id
		 *
		 * @return mixed
		 */
		public function load($id);

		/**
		 * invalidate whole cache storage
		 *
		 * @return $this
		 */
		public function invalidate();
	}
