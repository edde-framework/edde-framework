<?php
	namespace Edde\Common\Upgrade;

	use Edde\Api\Upgrade\IUpgrade;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractUpgrade extends AbstractUsable implements IUpgrade {
		/**
		 * @var string
		 */
		protected $version;

		/**
		 * @param string $version
		 */
		public function __construct($version) {
			$this->version = $version;
		}

		public function getVersion() {
			return $this->version;
		}

		public function upgrade() {
			$this->usse();
			$this->onUpgrade();
		}

		abstract protected function onUpgrade();
	}
