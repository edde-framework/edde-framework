<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;

	trait TemplateTrait {
		public function template(string $file) {
			if (($this instanceof IHtmlControl) === false) {
				throw new HtmlException(sprintf('Cannot use template trait on [%s]; it can be used only on [%s].', get_class($this), IHtmlControl::class));
			}
			return $this;
		}
	}
