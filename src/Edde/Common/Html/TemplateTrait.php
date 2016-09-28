<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Html\IHtmlTemplate;
	use Edde\Api\Html\IHtmlView;
	use Edde\Api\Template\LazyTemplateManagerTrait;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Template trait can be used by any html control; it gives simple way to load a template (or snippet) with some little magic around.
	 */
	trait TemplateTrait {
		use LazyContainerTrait;
		use LazyTemplateManagerTrait;
		use LazyRequestTrait;

		public function template(string $layout = null, array $snippetList = null, array $importList = [], string $class = null) {
			$this->check();
			if ($layout === null) {
				$reflectionClass = new \ReflectionClass($class ?: $this);
				$directory = dirname($reflectionClass->getFileName());
				$fileList = [
					$directory . '/../template/layout.xml',
					$directory . '/layout.xml',
					$directory . '/template/layout.xml',
					$action = dirname($reflectionClass->getFileName()) . '/template/' . StringUtils::recamel($this->request->getMethod()) . '.xml',
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
