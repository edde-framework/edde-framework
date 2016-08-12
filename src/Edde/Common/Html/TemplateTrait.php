<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Template\ITemplateManager;

	trait TemplateTrait {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ITemplateManager
		 */
		protected $templateManager;

		public function injectContainer(IContainer $container) {
			$this->container = $container;
		}

		public function injectTemplateManager(ITemplateManager $templateManager) {
			$this->templateManager = $templateManager;
		}

		public function template(string $file, array $variableList = []) {
			if (($this instanceof IHtmlControl) === false) {
				throw new HtmlException(sprintf('Cannot use template trait on [%s]; it can be used only on [%s].', get_class($this), IHtmlControl::class));
			}
			$template = $this->templateManager->template($file)
				->getInstance($this->container);
			$template->template($this);
			return $this;
		}
	}
