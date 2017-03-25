<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template;

	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Template\LazyTemplateManagerTrait;

	/**
	 * General template support; $this is used as a context.
	 */
	trait TemplateTrait {
		use LazyTemplateManagerTrait;
		use LazyResponseManagerTrait;

		/**
		 * prepare template to be sent as a application response; template is not actually executed
		 *
		 * @param string|null $name
		 */
		public function template(string $name = null) {
			$this->responseManager->response(new TemplateResponse($template = $this->templateManager->template()));
			$template->template($name ?: 'layout', $this, null, $this);
		}
	}
