<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Router\IRoute;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Strings\StringUtils;

	trait TemplateTrait {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ITemplateManager
		 */
		protected $templateManager;
		/**
		 * @var IRoute
		 */
		protected $route;

		public function injectContainer(IContainer $container) {
			$this->container = $container;
		}

		public function injectTemplateManager(ITemplateManager $templateManager) {
			$this->templateManager = $templateManager;
		}

		public function lazyRoute(IRoute $route) {
			$this->route = $route;
		}

		public function template(string $file = null) {
			if (($this instanceof IHtmlControl) === false) {
				throw new HtmlException(sprintf('Cannot use template trait on [%s]; it can be used only on [%s].', get_class($this), IHtmlControl::class));
			}
			if ($file === null) {
				$reflectionClass = new \ReflectionClass($this);
				$file = dirname($reflectionClass->getFileName()) . '/template/' . StringUtils::recamel($this->route->getMethod()) . '.xml';
			}
			$template = $this->templateManager->template($file)
				->getInstance($this->container);
			$template->template($this);
			return $this;
		}
	}
