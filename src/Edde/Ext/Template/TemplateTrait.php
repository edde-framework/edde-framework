<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template;

	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Log\LazyLogServiceTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Router\LazyRouterServiceTrait;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateContext;
	use Edde\Api\Template\LazyTemplateManagerTrait;
	use Edde\Api\Template\TemplateContextException;
	use Edde\Common\Protocol\Request\Response;

	/**
	 * General template support; $this is used as a context.
	 */
	trait TemplateTrait {
		use LazyResponseManagerTrait;
		use LazyTemplateManagerTrait;
		use LazyLogServiceTrait;
		use LazyContainerTrait;
		use LazyRouterServiceTrait;
		/**
		 * when template method is called, this variable holds current template reference
		 *
		 * @var ITemplate
		 */
		protected $template;

		/**
		 * prepare template to be sent as a application response; template is not actually executed
		 *
		 * @param ITemplateContext|string|null $context
		 * @param string|null                  $name
		 *
		 * @return IElement
		 * @throws TemplateContextException
		 */
		public function template($context = null, string $name = null): IElement {
			/** @var $context ITemplateContext */
			if ($context === 'this') {
				$context = $this;
			} else if (is_string($context)) {
				$context = $this->container->create($context, [], __METHOD__);
			} else if ($context === null) {
				$context = ($context = $this->getContextName()) !== null ? $this->container->create((string)$context, [], __METHOD__) : $this;
			}
			if ($context instanceof ITemplateContext === false) {
				throw new TemplateContextException(sprintf('Given template context [%s] does not implement interface [%s].', get_class($context), ITemplateContext::class));
			}
			$context->setElement($this->routerService->createRequest());
			$this->responseManager->response($content = new TemplateContent($this->template = $this->templateManager->template()->template($name ?: 'layout', $context, get_class($context), $context)));
			return (new Response())->setValue($content);
		}

		public function getContextName() {
			return null;
		}
	}
