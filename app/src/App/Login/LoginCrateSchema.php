<?php
	namespace App\Login;

	use Edde\Common\Schema\Schema;
	use Edde\Common\Schema\SchemaProperty;

	class LoginCrateSchema extends Schema {
		public function __construct() {
			parent::__construct(LoginCrate::class);
		}

		public function getLoginProperty() {
			return $this->getProperty('login');
		}

		public function getPasswordProperty() {
			return $this->getProperty('password');
		}

		protected function prepare() {
			$this->addPropertyList([
				new SchemaProperty($this, 'login'),
				new SchemaProperty($this, 'password'),
			]);
		}
	}
