<?php
	declare(strict_types=1);

	namespace Edde\Ext\Control;

	use Edde\Api\Router\LazyRouterServiceTrait;
	use Edde\Common\Control\AbstractControl;
	use Edde\Common\Strings\StringUtils;
	use Edde\Ext\Template\TemplateTrait;

	abstract class AbstractTemplateControl extends AbstractControl {
		use LazyRouterServiceTrait;
		use TemplateTrait;

		public function getCurrentTemplate() {
			return StringUtils::recamel((string)$this->routerService->createRequest()->getMeta('::method'));
		}
	}
