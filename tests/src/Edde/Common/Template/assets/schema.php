<?php
	declare(strict_types = 1);

	namespace Foo\Bar;

	use Edde\Common\Schema\Schema;
	use Edde\Common\Schema\SchemaProperty;

	class LoginSchema extends Schema {
		protected function prepare() {
			$this->addProperty(new SchemaProperty($this, 'login'));
			$this->addProperty(new SchemaProperty($this, 'password'));
		}
	}
