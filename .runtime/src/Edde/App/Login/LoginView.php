<?php
	declare(strict_types=1);

	namespace Edde\App\Login;

	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Common\Strings\StringUtils;
	use Edde\Ext\Control\AbstractTemplateControl;

	class LoginView extends AbstractTemplateControl {
		use LazyRequestTrait;

		public function getAction() {
			return StringUtils::recamel($this->request->getAction());
		}

		public function handleLogin() {
		}
	}
