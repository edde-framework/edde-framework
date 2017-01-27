<?php
	declare(strict_types = 1);

	namespace Edde\Ext\TemplateEngine;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\TemplateEngine\ICompiler;
	use Edde\Common\Config\AbstractConfigurator;

	class CompilerConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param ICompiler $instance
		 */
		public function config($instance) {
			$instance->registerMacroSet(DefaultMacroSet::macroSet($this->container));
			$instance->registerHelperSet(DefaultMacroSet::helperSet($this->container));
		}
	}
