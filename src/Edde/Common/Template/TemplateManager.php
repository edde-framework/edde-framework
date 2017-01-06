<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\File\FileException;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\LazyHelperSetTrait;
	use Edde\Api\Template\LazyMacroSetTrait;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\File\File;
	use Edde\Common\Object;

	/**
	 * Default implementation of a template manager.
	 */
	class TemplateManager extends Object implements ITemplateManager {
		use LazyMacroSetTrait;
		use LazyHelperSetTrait;
		use CacheTrait;

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function template(string $template, array $importList = []) {
			$cache = $this->cache();
			if ($result = $cache->load($cacheId = $template . implode(',', $importList))) {
				return $result;
			}
			/** @var $compiler ICompiler */
			$compiler = $this->container->create(Compiler::class, [new File($template)], __METHOD__);
			foreach ($importList as &$import) {
				$import = new File($import);
			}
			unset($import);
			$compiler->registerMacroSet($this->macroSet);
			$compiler->registerHelperSet($this->helperSet);
			return $cache->save($cacheId, $compiler->template($importList));
		}
	}
