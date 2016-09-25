<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Template\IHelperSet;
	use Edde\Api\Template\IMacroSet;
	use Edde\Common\AbstractObject;
	use Edde\Common\Html\Helper\MethodHelper;
	use Edde\Common\Html\Inline\PassChildInline;
	use Edde\Common\Html\Inline\PassInline;
	use Edde\Common\Html\Inline\SnippetInline;
	use Edde\Common\Html\Macro\ButtonMacro;
	use Edde\Common\Html\Macro\CallMacro;
	use Edde\Common\Html\Macro\ControlMacro;
	use Edde\Common\Html\Macro\CssMacro;
	use Edde\Common\Html\Macro\HeaderMacro;
	use Edde\Common\Html\Macro\HtmlMacro;
	use Edde\Common\Html\Macro\JsMacro;
	use Edde\Common\Html\Macro\LoadMacro;
	use Edde\Common\Html\Macro\LoopMacro;
	use Edde\Common\Html\Macro\PassMacro;
	use Edde\Common\Html\Macro\UseMacro;
	use Edde\Common\Html\PlaceholderControl;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\Tag\SpanControl;
	use Edde\Common\Template\HelperSet;
	use Edde\Common\Template\Inline\BlockInline;
	use Edde\Common\Template\Inline\IncludeInline;
	use Edde\Common\Template\Macro\BlockMacro;
	use Edde\Common\Template\Macro\ImportMacro;
	use Edde\Common\Template\Macro\IncludeMacro;
	use Edde\Common\Template\MacroSet;

	/**
	 * Factory class for default macro and helper set creation.
	 */
	class DefaultMacroSet extends AbstractObject {
		/**
		 * factory method for default set of macros; they are created on demand (when requested macro list)
		 *
		 * @param IContainer $container
		 *
		 * @return IMacroSet
		 */
		static public function macroSet(IContainer $container): IMacroSet {
			$macroSet = new MacroSet();
			$macroSet->onSetup(function (MacroSet $macroSet) use ($container) {
				$macroSet->setMacroList([
					$container->inject(new ControlMacro()),
					$container->inject(new ImportMacro()),
					$container->inject(new LoadMacro()),
					$container->inject(new IncludeMacro()),
					$container->inject(new BlockMacro()),
					$container->inject(new LoopMacro()),
					$container->inject(new UseMacro()),
					$container->inject(new CallMacro()),
					$container->inject(new CssMacro()),
					$container->inject(new JsMacro()),
					$container->inject(new PassMacro()),
					$container->inject(new HtmlMacro('div', DivControl::class)),
					$container->inject(new HtmlMacro('span', SpanControl::class)),
					$container->inject(new HtmlMacro('placeholder', PlaceholderControl::class)),
					$container->inject(new HeaderMacro('h1')),
					$container->inject(new HeaderMacro('h2')),
					$container->inject(new HeaderMacro('h3')),
					$container->inject(new HeaderMacro('h4')),
					$container->inject(new HeaderMacro('h5')),
					$container->inject(new HeaderMacro('h6')),
					$container->inject(new ButtonMacro()),
				]);
				$macroSet->setInlineList([
					$container->inject(new BlockInline()),
					$container->inject(new IncludeInline()),
					$container->inject(new SnippetInline()),
					$container->inject(new PassInline()),
					$container->inject(new PassChildInline()),
				]);
			});
			return $macroSet;
		}

		/**
		 * factory method for default set of helpers; they are created on demand (when requested)
		 *
		 * @param IContainer $container
		 *
		 * @return IHelperSet
		 */
		static public function helperSet(IContainer $container): IHelperSet {
			$helperSet = new HelperSet();
			$helperSet->onSetup(function (IHelperSet $helperSet) use ($container) {
				$helperSet->registerHelper($container->inject(new MethodHelper()));
			});
			return $helperSet;
		}
	}
