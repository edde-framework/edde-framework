<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Value;

	use Edde\Api\Html\IHtmlValueControl;
	use Edde\Common\Html\AbstractHtmlControl;

	abstract class HtmlValueControl extends AbstractHtmlControl implements IHtmlValueControl {
		protected function prepare() {
			parent::prepare();
			$this->addClass('edde-value');
		}
	}
