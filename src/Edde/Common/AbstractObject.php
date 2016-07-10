<?php
	namespace Edde\Common;

	use Edde\Api\EddeException;

	abstract class AbstractObject {
		public function __get($name) {
			throw new EddeException(sprintf('Reading from the undefined/private/protected property [%s::$%s].', static::class, $name));
		}

		public function __set($name, $value) {
			throw new EddeException(sprintf('Writing to the undefined/private/protected property [%s::$%s].', static::class, $name));
		}
	}
