<?php
	declare(strict_types = 1);

	namespace Edde\Common\Serialize;

	use Edde\Api\Serialize\IHashable;

	trait SerializableTrait {
		public function __sleep() {
			HashIndex::save($this);
			return array_keys(get_object_vars($this));
		}

		public function __wakeup() {
			HashIndex::save($this);
			foreach ($this as $k => $v) {
				if ($v instanceof IHashable) {
					$this->{$k} = HashIndex::load($v->hash());
				}
			}
		}
	}
