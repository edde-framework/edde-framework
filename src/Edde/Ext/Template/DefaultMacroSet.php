<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Common\AbstractObject;
	use Edde\Common\Html\Macro\ButtonMacro;
	use Edde\Common\Html\Macro\ControlMacro;
	use Edde\Common\Html\Macro\CssMacro;
	use Edde\Common\Html\Macro\HtmlMacro;
	use Edde\Common\Html\Macro\JsMacro;
	use Edde\Common\Html\PlaceholderControl;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\Tag\SpanControl;
	use Edde\Common\Template\Inline\BlockInline;
	use Edde\Common\Template\Inline\IncludeInline;
	use Edde\Common\Template\Macro\BlockMacro;
	use Edde\Common\Template\Macro\IncludeMacro;
	use Edde\Common\Template\Macro\UseMacro;
	use Edde\Common\Template\MacroSet;

	class DefaultMacroSet extends AbstractObject {
		static public function factory(IContainer $container) {
			$macroSet = new MacroSet();
			$macroSet->onSetup(function (MacroSet $macroSet) use ($container) {
				$macroSet->setMacroList([
					$container->inject(new UseMacro()),
					$container->inject(new IncludeMacro()),
					$container->inject(new BlockMacro()),
					$container->inject(new ControlMacro()),
					$container->inject(new CssMacro()),
					$container->inject(new JsMacro()),
					$container->inject(new HtmlMacro('div', DivControl::class)),
					$container->inject(new HtmlMacro('span', SpanControl::class)),
					$container->inject(new HtmlMacro('placeholder', PlaceholderControl::class)),
					$container->inject(new ButtonMacro()),
				]);
				$macroSet->setInlineList([
					$container->inject(new BlockInline()),
					$container->inject(new IncludeInline()),
				]);
			});
			return $macroSet;
		}
	}
