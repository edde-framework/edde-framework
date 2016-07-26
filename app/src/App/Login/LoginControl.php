<?php
	namespace App\Login;

	use Edde\Common\Control\Html\EddeHtmlControl;

	class LoginControl extends EddeHtmlControl {
		/**
		 * @var LoginCrateSchema
		 */
		protected $loginCrateSchema;

		final public function lazyLoginCrateSchema(LoginCrateSchema $loginCrateSchema) {
			$this->loginCrateSchema = $loginCrateSchema;
		}

		public function actionLogin() {
			$this->setTitle('Login');
			$divControl = $this->createDivControl();
			$divControl->setId('foo');
			$divControl->createTextInputControl($this->loginCrateSchema->getLoginProperty());
			$divControl->createPasswordInputControl($this->loginCrateSchema->getPasswordProperty());
			$divControl->createButtonControl('login', static::class, 'OnLogin')
				->bind($divControl);
			$this->send();
		}

		public function handleOnLogin() {
		}

		protected function onPrepare() {
		}
	}
