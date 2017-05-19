<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Template\LazyTemplateManagerTrait;

	/**
	 * General template support; $this is used as a context.
	 */
	trait TemplateTrait {
		use LazyTemplateManagerTrait;

		/**
		 * prepare template to be sent as a application response; template is not actually executed
		 *
		 * @param string|null $name
		 *
		 * @return IElement
		 */
		public function template(string $name = null): IElement {
			$template = $this->templateManager->template();
			return new TemplateResponse($template->template($name ?: 'layout', $this, static::class, $this));
		}
	}
