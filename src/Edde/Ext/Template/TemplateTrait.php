<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template;

	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Template\LazyTemplateManagerTrait;

	/**
	 * General template support; $this is used as a context.
	 */
	trait TemplateTrait {
		use LazyResponseManagerTrait;
		use LazyTemplateManagerTrait;

		/**
		 * prepare template to be sent as a application response; template is not actually executed
		 *
		 * @param string|null $name
		 */
		public function template(string $name = null) {
			$template = $this->templateManager->template();
			$this->responseManager->response(new TemplateContent($template->template($name ?: 'layout', $this, static::class, $this)));
		}
	}
