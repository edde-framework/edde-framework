<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Html\IHtmlTemplate;
	use Edde\Api\Html\IHtmlView;
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
			$this->check();
			if ($layout === null) {
				$reflectionClass = new \ReflectionClass($this);
				$directory = dirname($reflectionClass->getFileName());
				$fileList = [
					$directory . '/../template/layout.xml',
					$directory . '/layout.xml',
					$directory . '/template/layout.xml',
					$action = $this->getActionTemplateFile(),
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
			/** @var $control IHtmlView */
			/** @var $template IHtmlTemplate */
			$control = $this;
			$template = $this->templateManager->template($layout)
				->getInstance($this->container);
			foreach ($useList as $use) {
				$template->import($use);
			}
			$template->template($control);
			return $this;
		}

		protected function check() {
			if (($this instanceof IHtmlControl) === false) {
				throw new HtmlException(sprintf('Cannot use template trait on [%s]; it can be used only on [%s].', get_class($this), IHtmlControl::class));
			}
		}

		protected function getActionTemplateFile() {
			$reflectionClass = new \ReflectionClass($this);
			return dirname($reflectionClass->getFileName()) . '/template/' . StringUtils::recamel($this->route->getMethod()) . '.xml';
		}

		public function block(string $file, string ...$blockList) {
			$this->check();
			/** @var $template IHtmlTemplate */
			$template = $this->templateManager->template($file = $file ?: $this->getActionTemplateFile())
				->getInstance($this->container);
			/** @var $control IHtmlView */
			$control = $this;
			$template->template($control);
			foreach ($blockList as $block) {
				$template->control($block, $control);
			}
			return $this;
		}
	}
