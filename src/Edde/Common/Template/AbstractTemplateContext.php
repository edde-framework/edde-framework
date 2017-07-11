<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Router\LazyRouterServiceTrait;
	use Edde\Api\Template\ITemplateContext;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;
	use Edde\Common\Strings\StringUtils;

	abstract class AbstractTemplateContext extends Object implements ITemplateContext {
		use LazyRouterServiceTrait;
		use ConfigurableTrait;
		/**
		 * @var IElement
		 */
		protected $element;

		/**
		 * method could be used from a template t oget current request method name (action-<action name>; action-index, ...)
		 *
		 * @return string
		 */
		public function getCurrentMethod(): string {
			return StringUtils::recamel((string)$this->routerService->createRequest()->getMeta('::method'));
		}

		/**
		 * @inheritdoc
		 */
		public function setElement(IElement $element): ITemplateContext {
			$this->element = $element;
			return $this;
		}
	}
