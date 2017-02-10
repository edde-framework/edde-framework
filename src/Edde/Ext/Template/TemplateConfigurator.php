<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Template\Macro\HtmlMacro;
	use Edde\Common\Template\Macro\IncludeMacro;
	use Edde\Common\Template\Macro\SnippetMacro;

	class TemplateConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param ITemplate $instance
		 */
		public function config($instance) {
			$macroList = [
				SnippetMacro::class,
				IncludeMacro::class,
				HtmlMacro::class,
			];
			foreach ($macroList as $name) {
				/** @var $macro IMacro */
				$macro = $this->container->create($name);
				$macro->register($instance);
			}
		}
	}
