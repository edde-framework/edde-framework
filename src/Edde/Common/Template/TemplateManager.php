<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
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

		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}

		public function template(string $template, array $importList = []) {
			$this->container->inject($compiler = new Compiler(new File($template)));
			return $compiler->template();
		}

		protected function prepare() {
			$this->cache();
		}
	}
