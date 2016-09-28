<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\File\FileException;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IHelperSet;
	use Edde\Api\Template\IMacroSet;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\File\File;

	/**
	 * Default implementation of a template manager.
	 */
	class TemplateManager extends AbstractDeffered implements ITemplateManager {
		use CacheTrait;
		use LazyContainerTrait;
		/**
		 * @var IMacroSet
		 */
		protected $macroSet;
		/**
		 * @var IHelperSet
		 */
		protected $helperSet;

		/**
		 * @param IMacroSet $macroSet
		 */
		public function lazyMacroSet(IMacroSet $macroSet) {
			$this->macroSet = $macroSet;
		}

		/**
		 * @param IHelperSet $helperSet
		 */
		public function lazyHelperSet(IHelperSet $helperSet) {
			$this->helperSet = $helperSet;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function template(string $template, array $importList = []) {
			$this->use();
			if ($result = $this->cache->load($cacheId = $template . implode(',', $importList))) {
				return $result;
			}
			/** @var $compiler ICompiler */
			$this->container->inject($compiler = new Compiler(new File($template)));
			foreach ($importList as &$import) {
				$import = new File($import);
			}
			unset($import);
			$compiler->registerMacroSet($this->macroSet);
			$compiler->registerHelperSet($this->helperSet);
			return $this->cache->save($cacheId, $compiler->template($importList));
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			$this->cache();
		}
	}
