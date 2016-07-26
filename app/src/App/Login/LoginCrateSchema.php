<?php
	namespace App\Login;

	use Edde\Common\Schema\Property;
	use Edde\Common\Schema\Schema;

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
				new Property($this, 'login'),
				new Property($this, 'password'),
			]);
		}
	}
