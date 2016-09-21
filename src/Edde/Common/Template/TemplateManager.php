<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Template\IMacroSet;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\File\File;
	use Edde\Common\Usable\AbstractUsable;

	class TemplateManager extends AbstractUsable implements ITemplateManager {
		use CacheTrait;
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var IMacroSet
		 */
		protected $macroSet;

		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}

		public function lazyMacroSet(IMacroSet $macroSet) {
			$this->macroSet = $macroSet;
		}

		public function template(string $template, array $importList = []) {
			$this->container->inject($compiler = new Compiler(new File($template)));
			foreach ($importList as &$import) {
				$import = new File($import);
			}
			$compiler->set($this->macroSet);
			return $compiler->template($importList);
		}

		protected function prepare() {
			$this->cache();
		}
	}
