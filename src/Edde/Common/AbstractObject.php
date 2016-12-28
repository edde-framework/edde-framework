<?php
	declare(strict_types = 1);

	namespace Edde\Common;

	use Edde\Api\Container\ILazyInject;

	abstract class AbstractObject implements ILazyInject, \Serializable {
		use ObjectTrait;

		public function serialize() {
			return serialize(get_object_vars($this));
		}

		public function unserialize($serialized) {
			/** @noinspection UnserializeExploitsInspection */
			/** @noinspection ForeachSourceInspection */
			foreach (unserialize($serialized) as $k => $v) {
				$this->$k = $v;
			}
		}
	}
