<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Html\IHtmlTemplate;
	use Edde\Api\Html\IHtmlView;
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
		 * @var IRequest
		 */
		protected $request;

		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}

		public function lazyTemplateManager(ITemplateManager $templateManager) {
			$this->templateManager = $templateManager;
		}

		public function lazyRequest(IRequest $request) {
			$this->request = $request;
		}

		public function template(string $layout = null, array $snippetList = null, array $importList = []) {
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
					$importList[] = $action;
				}
			}
			$this->snippet($layout, $snippetList, $importList);
			return $this;
		}

		protected function check() {
			if (($this instanceof IHtmlControl) === false) {
				throw new HtmlException(sprintf('Cannot use template trait on [%s]; it can be used only on [%s].', get_class($this), IHtmlControl::class));
			}
		}

		protected function getActionTemplateFile() {
			$reflectionClass = new \ReflectionClass($this);
			return dirname($reflectionClass->getFileName()) . '/template/' . StringUtils::recamel($this->request->getMethod()) . '.xml';
		}

		public function snippet(string $file, array $snippetList = null, array $importList = []) {
			$this->check();
			/** @var $control IHtmlView */
			/** @var $template IHtmlTemplate */
			$control = $this;
			$template = AbstractHtmlTemplate::template($this->templateManager->template($file, $importList), $this->container);
			foreach ($snippetList ?: [null] as $snippet) {
				$template->snippet($control, $snippet);
			}
			return $this;
		}
	}
