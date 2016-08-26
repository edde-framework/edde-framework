<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Html\HtmlException;
	use Edde\Api\Template\IMacro;
	use Edde\Common\Template\Macro\Control\ControlMacro;

	/**
	 * Simple invisible control used for controls created via ajax.
	 */
	class PlaceholderControl extends AbstractHtmlControl {
		static public function macro(): IMacro {
			return new ControlMacro('placeholder', static::class);
		}

		public function setTag(string $tag, bool $pair = true) {
			throw new HtmlException(sprintf('Cannot set tag [%s] to a placeholder control.', $tag));
		}

		public function isPair() {
			return true;
		}

		protected function prepare() {
			parent::prepare();
			parent::setTag('div');
			$this->addClass('edde-placeholder');
		}
	}
