<?php
	declare(strict_types=1);

	namespace Edde\App\Index;

	use Edde\Common\Strings\StringUtils;
	use Edde\Ext\Control\AbstractTemplateControl;

	class IndexView extends AbstractTemplateControl {
		public function getAction() {
			return StringUtils::recamel($this->request->getAction());
		}
	}
