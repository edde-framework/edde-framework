<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Config\AbstractConfigurator;
	use Edde\Common\Template\Macro\ForeachMacro;
	use Edde\Common\Template\Macro\HtmlMacro;
	use Edde\Common\Template\Macro\IfMacro;
	use Edde\Common\Template\Macro\IncludeMacro;
	use Edde\Common\Template\Macro\LoadMacro;
	use Edde\Common\Template\Macro\SnippetMacro;
	use Edde\Common\Template\Macro\SwitchMacro;

	class TemplateConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param ITemplate $instance
		 */
		public function config($instance) {
			$macroList = [
				SnippetMacro::class,
				IncludeMacro::class,
				LoadMacro::class,
				HtmlMacro::class,
				ForeachMacro::class,
				IfMacro::class,
				SwitchMacro::class,
			];
			foreach ($macroList as $name) {
				/** @var $macro IMacro */
				$macro = $this->container->create($name);
				$macro->register($instance);
			}
		}
	}
