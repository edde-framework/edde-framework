<?php
	namespace App\Login;

	use Edde\Common\Crate\Crate;

	class LoginCrate extends Crate {
		public function __construct(LoginCrateSchema $loginCrateSchema) {
			parent::__construct($loginCrateSchema);
		}
	}
