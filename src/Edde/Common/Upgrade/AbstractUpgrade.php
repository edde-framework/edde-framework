<?php
	declare(strict_types = 1);

	namespace Edde\Common\Upgrade;

	use Edde\Api\Upgrade\IUpgrade;
	use Edde\Common\AbstractObject;

	abstract class AbstractUpgrade extends AbstractObject implements IUpgrade {
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
			$this->use();
			$this->onUpgrade();
		}

		abstract protected function onUpgrade();
	}
