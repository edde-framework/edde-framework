<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\File\FileException;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\AbstractObject;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\File\File;

	/**
	 * Default implementation of a template manager.
	 */
	class TemplateManager extends AbstractObject implements ITemplateManager {
		use LazyContainerTrait;
		use CacheTrait;

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function template(string $template, array $importList = []) {
			if ($result = $this->cache->load($cacheId = $template . implode(',', $importList))) {
				return $result;
			}
			/** @var $compiler ICompiler */
			$compiler = $this->container->create(Compiler::class, new File($template));
			foreach ($importList as &$import) {
				$import = new File($import);
			}
			unset($import);
			$compiler->registerMacroSet($this->macroSet);
			$compiler->registerHelperSet($this->helperSet);
			return $this->cache->save($cacheId, $compiler->template($importList));
		}

		protected function prepare() {
			parent::prepare();
			$this->cache();
		}
	}
