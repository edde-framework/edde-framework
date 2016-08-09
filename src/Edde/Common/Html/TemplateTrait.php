<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Template\ITemplateFactory;

	trait TemplateTrait {
		/**
		 * @var ITemplateFactory
		 */
		protected $templatetemplateFactory;

		public function lazyTemplate(ITemplateFactory $template) {
			$this->templatetemplateFactory = $template;
		}

		public function template(string $file) {
			if (($this instanceof IHtmlControl) === false) {
				throw new HtmlException(sprintf('Cannot use template trait on [%s]; it can be used only on [%s].', get_class($this), IHtmlControl::class));
			}
			$this->templatetemplateFactory->build($file, $this);
			return $this;
		}
	}
