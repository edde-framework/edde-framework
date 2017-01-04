<?php
	declare(strict_types = 1);

	namespace Edde\Common\Serialize;

	trait SerializableTrait {
		public function __sleep() {
			HashIndex::save($this);
			return array_keys(get_object_vars($this));
		}

//		public function __wakeup() {
//		}
	}
