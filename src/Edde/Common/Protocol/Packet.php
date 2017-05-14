<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	class Packet extends Element {
		public function __construct(string $origin) {
			parent::__construct('packet');
			$this->setAttribute('version', '1.1');
			$this->setAttribute('origin', $origin);
		}
	}
