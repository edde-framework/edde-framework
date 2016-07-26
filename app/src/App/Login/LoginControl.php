<?php
	namespace App\Login;

	use Edde\Api\Schema\ISchemaManager;
	use Edde\Common\Control\Html\EddeHtmlControl;

	class LoginControl extends EddeHtmlControl {
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;
		/**
		 * @var LoginCrateSchema
		 */
		protected $loginCrateSchema;

		final public function lazySchemaManager(ISchemaManager $schemaManager) {
			$this->schemaManager = $schemaManager;
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

		public function handleOnLogin(LoginCrate $loginCrate) {
		}

		protected function onPrepare() {
			$this->loginCrateSchema = $this->schemaManager->getSchema(LoginCrate::class);
		}
	}
