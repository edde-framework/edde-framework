<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template;

	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Template\LazyTemplateManagerTrait;

	/**
	 * General template support; $this is used as a context.
	 */
	trait TemplateTrait {
		use LazyResponseManagerTrait;
		use LazyTemplateManagerTrait;
		use LazyContainerTrait;

		/**
		 * prepare template to be sent as a application response; template is not actually executed
		 *
		 * @param string|null $name
		 * @param object|null $context
		 */
		public function template(string $name = null, $context = null) {
			$this->responseManager->response(new TemplateContent($this->templateManager->template()->template($name ?: 'layout', $context = ($context ? (is_string($context) ? $this->container->create($context) : $context) : $this), get_class($context), $context)));
		}
	}
