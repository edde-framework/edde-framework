<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Tag;

	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Template\IMacro;
	use Edde\Common\Html\AbstractHtmlControl;
	use Edde\Common\Template\Macro\Control\ControlMacro;

	class SpanControl extends AbstractHtmlControl {
		static public function macro(): IMacro {
			return new ControlMacro('span', static::class);
		}

		public function setTag(string $tag, bool $pair = true): IHtmlControl {
			throw new HtmlException(sprintf('Cannot set tag [%s] to a span control.', $tag));
		}

		public function isPair() {
			return true;
		}

		protected function prepare() {
			parent::prepare();
			parent::setTag('span');
		}
	}
