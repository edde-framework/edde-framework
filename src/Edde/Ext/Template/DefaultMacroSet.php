<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Template\IHelperSet;
	use Edde\Api\Template\IMacroSet;
	use Edde\Common\Object;
	use Edde\Common\Template\HelperSet;
	use Edde\Common\Template\MacroSet;

	/**
	 * Factory class for default macro and helper set creation.
	 */
	class DefaultMacroSet extends Object {
		/**
		 * cache method for default set of macros; they are created on demand (when requested macro list)
		 *
		 * @param IContainer $container
		 *
		 * @return IMacroSet
		 */
		static public function macroSet(IContainer $container): IMacroSet {
			$macroSet = new MacroSet();
//			$macroSet->registerOnUse(function (MacroSet $macroSet) use ($container) {
//				$macroSet->setMacroList([
//					$container->inject(new ImportMacro()),
//					$container->inject(new LoadMacro()),
//					$container->inject(new IncludeMacro()),
//					$container->inject(new LoopMacro()),
//					$container->inject(new IfMacro()),
//					$container->inject(new SwitchMacro()),
//					$container->inject(new CaseMacro()),
//					$container->inject(new UseMacro()),
//					$container->inject(new ControlMacro()),
//					$container->inject(new BlockMacro()),
//					$container->inject(new CallMacro()),
//					$container->inject(new CssMacro()),
//					$container->inject(new JsMacro()),
//					$container->inject(new SchemaMacro()),
//					$container->inject(new PropertyMacro()),
//					$container->inject(new PassMacro()),
//					$container->inject(new PassChildMacro()),
//					$container->inject(new SnippetMacro()),
//					$container->inject(new TranslatorMacro()),
//					$container->inject(new DictionaryMacro()),
//					$container->inject(new HtmlMacro('div', DivControl::class)),
//					$container->inject(new HtmlMacro('span', SpanControl::class)),
//					$container->inject(new HtmlMacro('p', ParagraphControl::class)),
//					$container->inject(new HtmlMacro('img', ImgControl::class)),
//					$container->inject(new HtmlMacro('text', TextControl::class)),
//					$container->inject(new HtmlMacro('password', PasswordControl::class)),
//					$container->inject(new HtmlMacro('placeholder', PlaceholderControl::class)),
//					$container->inject(new HtmlMacro('table', TableControl::class)),
//					$container->inject(new HtmlMacro('thead', TableHeadControl::class)),
//					$container->inject(new HtmlMacro('tbody', TableBodyControl::class)),
//					$container->inject(new HtmlMacro('tfoot', TableFootControl::class)),
//					$container->inject(new HtmlMacro('td', TableCellControl::class)),
//					$container->inject(new HtmlMacro('tr', TableRowControl::class)),
//					$container->inject(new HtmlMacro('th', TableHeaderControl::class)),
//					$container->inject(new HtmlMacro('caption', CaptionControl::class)),
//					$container->inject(new HtmlMacro('col', ColumnControl::class)),
//					$container->inject(new HtmlMacro('colgroup', ColumnGroupControl::class)),
//					$container->inject(new HtmlMacro('blockquote', BlockquoteControl::class)),
//					$container->inject(new HtmlMacro('section', SectionControl::class)),
//					$container->inject(new HeaderMacro('h1')),
//					$container->inject(new HeaderMacro('h2')),
//					$container->inject(new HeaderMacro('h3')),
//					$container->inject(new HeaderMacro('h4')),
//					$container->inject(new HeaderMacro('h5')),
//					$container->inject(new HeaderMacro('h6')),
//					$container->inject(new ButtonMacro()),
//					$container->inject(new FillMacro()),
//					$container->inject(new TitleMacro()),
//					$container->inject(new AttrMacro()),
//				]);
//			});
			return $macroSet;
		}

		/**
		 * cache method for default set of helpers; they are created on demand (when requested)
		 *
		 * @param IContainer $container
		 *
		 * @return IHelperSet
		 */
		static public function helperSet(IContainer $container): IHelperSet {
			$helperSet = new HelperSet();
//			$helperSet->registerOnUse(function (IHelperSet $helperSet) use ($container) {
//				$helperSet->registerHelper($container->inject(new MethodHelper()));
//				$helperSet->registerHelper($container->inject(new TranslateHelper()));
//			});
			return $helperSet;
		}
	}
