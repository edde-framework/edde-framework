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

		public function getContextName() {
			return implode('\\', array_slice(explode('\\', static::class), -2, 1)) . '\\' . str_replace('Action', '', StringUtils::toCamelCase((string)$this->routerService->createRequest()->getMeta('::method'))) . 'TemplateContext';
		}
	}
