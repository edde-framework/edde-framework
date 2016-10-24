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
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Template trait can be used by any html control; it gives simple way to load a template (or snippet) with some little magic around.
	 */
	trait TemplateTrait {
		use LazyContainerTrait;
		use LazyTemplateManagerTrait;
		use LazyRequestTrait;
		use CacheTrait;

		public function template(array $snippetList = null) {
			$reflectionClass = new \ReflectionClass($this);
			if (($template = $this->cache->load($cacheId = ('template-list/' . $this->request->getId() . $reflectionClass->getName()))) === null) {
				$parent = $reflectionClass;
				$fileList = [];
				while ($parent) {
					$directory = dirname($parent->getFileName());
					$fileList[] = $directory . '/../template/layout.xml';
					$fileList[] = $directory . '/layout.xml';
					$fileList[] = $directory . '/template/layout.xml';
					foreach ($this->request->getHandlerList() as $handler) {
						$fileList[] = $directory . '/template/' . StringUtils::recamel($handler[1]) . '.xml';
					}
					$parent = $parent->getParentClass();
				}
				$importList = [];
				foreach ($fileList as $file) {
					if (file_exists($file)) {
						if (strpos($file, 'layout.xml') !== false) {
							$layout = $file;
							continue;
						}
						$importList[] = $file;
					}
				}
				/** @noinspection UnSafeIsSetOverArrayInspection */
				if (isset($layout) === false) {
					$layout = array_shift($importList);
				}
				/** @noinspection PhpUndefinedVariableInspection */
				$this->cache->save($cacheId, $template = [
					$layout,
					$importList,
				]);
			}
			/** @noinspection PhpUndefinedVariableInspection */
			$this->snippet($template[0], $snippetList, $template[1]);
			return $this;
		}

		public function snippet(string $file, array $snippetList = null, array $importList = []) {
			if (($this instanceof IHtmlControl) === false) {
				throw new HtmlException(sprintf('Cannot use template trait on [%s]; it can be used only on [%s].', get_class($this), IHtmlControl::class));
			}
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
