<?php
	declare(strict_types=1);

	namespace Edde\Ext\Control;

	use Edde\Common\Control\AbstractControl;
	use Edde\Ext\Template\TemplateTrait;

	abstract class AbstractTemplateControl extends AbstractControl {
		use TemplateTrait;

		/**
		 * @inheritdoc
		 */
		protected function action(string $action, array $parameterList) {
			$this->template();
		}
	}
