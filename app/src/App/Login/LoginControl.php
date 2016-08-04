<?php
	declare(strict_types = 1);

	namespace App\Login;

	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Common\Html\EddeHtmlControl;

	class LoginControl extends EddeHtmlControl {
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;
		/**
		 * @var LoginCrateSchema
		 */
		protected $loginCrateSchema;
		/**
		 * @var ICryptEngine
		 */
		protected $cryptEngine;

		final public function lazySchemaManager(ISchemaManager $schemaManager) {
			$this->schemaManager = $schemaManager;
		}

		final public function lazyCryptEngine(ICryptEngine $cryptEngine) {
			$this->cryptEngine = $cryptEngine;
		}

		public function actionLogin() {
			$this->setTitle('Login');
			$divControl = $this->createDivControl();
			$divControl->setId($this->cryptEngine->guid());
			$divControl->createTextInputControl($this->loginCrateSchema->getLoginProperty());
			$divControl->createPasswordInputControl($this->loginCrateSchema->getPasswordProperty());
			$divControl->createButtonControl('login', static::class, 'OnLogin')
				->bind($divControl);
			$this->send();
		}

		public function handleOnLogin(LoginCrate $loginCrate) {
		}

		protected function prepare() {
			parent::prepare();
			$this->loginCrateSchema = $this->schemaManager->getSchema(LoginCrate::class);
		}
	}
