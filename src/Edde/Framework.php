<?php
	declare(strict_types = 1);

	namespace Edde;

	use Edde\Common\AbstractObject;

	class Framework extends AbstractObject {
		public function getVersionString() {
			return $this->getVersion() . ' - ' . $this->getCodename();
		}

		public function getVersion() {
			return '2.2.0.55';
		}

		public function getCodename() {
			return 'Jumping Ice Cube';
		}
	}
