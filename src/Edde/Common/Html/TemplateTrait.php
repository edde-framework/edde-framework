<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Html\IHtmlTemplate;
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

		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}

		public function lazyTemplateManager(ITemplateManager $templateManager) {
			$this->templateManager = $templateManager;
		}

		public function lazyRoute(IRoute $route) {
			$this->route = $route;
		}

		public function template(string $layout = null, ...$useList) {
			if (($this instanceof IHtmlControl) === false) {
				throw new HtmlException(sprintf('Cannot use template trait on [%s]; it can be used only on [%s].', get_class($this), IHtmlControl::class));
			}
			if ($layout === null) {
				$reflectionClass = new \ReflectionClass($this);
				$directory = dirname($reflectionClass->getFileName());
				$fileList = [
					$directory . '/../template/layout.xml',
					$directory . '/layout.xml',
					$directory . '/template/layout.xml',
					$action = ($directory . '/template/' . StringUtils::recamel($this->route->getMethod()) . '.xml'),
				];
				foreach ($fileList as $file) {
					if (file_exists($file)) {
						$layout = $file;
						break;
					}
				}
				if ($layout !== $action && file_exists($action)) {
					$useList[] = $action;
				}
			}
			/** @var $template IHtmlTemplate */
			$template = $this->templateManager->template($layout)
				->getInstance($this->container);
			$template->template($this, $useList);
			return $this;
		}
	}
