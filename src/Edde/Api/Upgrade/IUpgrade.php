<?php
	namespace Edde\Api\Upgrade;

	use Edde\Api\Usable\IUsable;

	/**
	 * Simple way how to run arbitrary application upgrades; this can be storage modification, filesystem operations, ...
	 */
	interface IUpgrade extends IUsable {
		/**
		 * version can be arbitrary string; for IUpgradeManager is important order of versions (they should NOT be parsed)
		 *
		 * @return string
		 */
		public function getVersion();

		/**
		 * run this particular upgrade
		 *
		 * @return $this
		 *
		 * @throws UpgradeException
		 */
		public function upgrade();
	}