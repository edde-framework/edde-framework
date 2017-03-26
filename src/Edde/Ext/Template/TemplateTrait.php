<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template;

	use Edde\Api\Application\IResponse;
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
		 * @return IResponse|TemplateResponse
		 */
		public function template(string $name = null): IResponse {
			$template = $this->templateManager->template();
			$template->template($name ?: 'layout', $this, null, $this);
			return new TemplateResponse($template);
		}
	}
