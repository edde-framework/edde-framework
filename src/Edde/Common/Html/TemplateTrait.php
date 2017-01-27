<?php
	declare(strict_types=1);

	namespace Edde\Common\Html;

	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Api\Control\ControlException;
	use Edde\Api\Html\HtmlException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Html\IHtmlTemplate;
	use Edde\Api\Html\IHtmlView;
	use Edde\Api\TemplateEngine\CompilerException;
	use Edde\Api\TemplateEngine\LazyTemplateManagerTrait;
	use Edde\Api\TemplateEngine\TemplateException;
	use Edde\Common\Cache\CacheTrait;

	/**
	 * Template trait can be used by any html control; it gives simple way to load a template (or snippet) with some little magic around.
	 */
	trait TemplateTrait {
		use LazyTemplateManagerTrait;
		use LazyRequestTrait;
		use CacheTrait;

		public function template(array $snippetList = null) {
			$reflectionClass = new \ReflectionClass($this);
			$cache = $this->cache();
			if (($template = $cache->load($cacheId = ('template-list/' . $this->request->getId() . $reflectionClass->getName()))) === null) {
				$parent = $reflectionClass;
				$fileList = [];
				while ($parent) {
					$directory = dirname($parent->getFileName());
					$fileList[] = $directory . '/layout.xml';
					$fileList[] = $directory . '/templates/layout.xml';
					$fileList[] = $directory . '/../templates/layout.xml';
					$fileList[] = $directory . '/../../templates/layout.xml';
					if ($this->request->hasAction()) {
						$fileList[] = $directory . '/' . ($action = 'action-' . $this->request->getActionName() . '.xml');
						$fileList[] = $directory . '/templates/' . $action;
					}
					if ($this->request->hasHandle()) {
						$fileList[] = $directory . '/' . ($handle = 'handle-' . $this->request->getHandleName() . '.xml');
						$fileList[] = $directory . '/templates/' . $handle;
					}
					$parent = $parent->getParentClass();
				}
				$fileList = array_reverse($fileList, true);
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
				$cache->save($cacheId, $template = [
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
			try {
				$this->templateManager->setup();
				$template = AbstractHtmlTemplate::template($this->templateManager->template($file, $importList), $this->container);
				foreach ($snippetList ?: [null] as $snippet) {
					$template->snippet($control, $snippet);
				}
			} catch (CompilerException $exception) {
				throw $exception;
			} catch (TemplateException $exception) {
				$message = 'Template has failed; ' . ($snippetList ? sprintf("source files:\n%s", implode(', ', $snippetList)) : 'there are no files in the snippet list. Action/handler template was probably not found.');
				throw new ControlException($message, 0, $exception);
			}
			return $this;
		}
	}
