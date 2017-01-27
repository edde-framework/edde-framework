<?php
	declare(strict_types = 1);

	namespace Edde\Ext\TemplateEngine;

	use Edde\Api\Container\IContainer;
	use Edde\Api\TemplateEngine\IHelperSet;
	use Edde\Api\TemplateEngine\IMacroSet;
	use Edde\Common\Html\Input\PasswordControl;
	use Edde\Common\Html\Input\TextControl;
	use Edde\Common\Html\Macro\AttrMacro;
	use Edde\Common\Html\Macro\ButtonMacro;
	use Edde\Common\Html\Macro\CallMacro;
	use Edde\Common\Html\Macro\CaseMacro;
	use Edde\Common\Html\Macro\ControlMacro;
	use Edde\Common\Html\Macro\CssMacro;
	use Edde\Common\Html\Macro\DictionaryMacro;
	use Edde\Common\Html\Macro\FillMacro;
	use Edde\Common\Html\Macro\HeaderMacro;
	use Edde\Common\Html\Macro\HtmlMacro;
	use Edde\Common\Html\Macro\IfMacro;
	use Edde\Common\Html\Macro\JsMacro;
	use Edde\Common\Html\Macro\LoadMacro;
	use Edde\Common\Html\Macro\LoopMacro;
	use Edde\Common\Html\Macro\PassChildMacro;
	use Edde\Common\Html\Macro\PassMacro;
	use Edde\Common\Html\Macro\PropertyMacro;
	use Edde\Common\Html\Macro\SchemaMacro;
	use Edde\Common\Html\Macro\SnippetMacro;
	use Edde\Common\Html\Macro\SwitchMacro;
	use Edde\Common\Html\Macro\TitleMacro;
	use Edde\Common\Html\Macro\TranslatorMacro;
	use Edde\Common\Html\Macro\UseMacro;
	use Edde\Common\Html\PlaceholderControl;
	use Edde\Common\Html\Tag\BlockquoteControl;
	use Edde\Common\Html\Tag\CaptionControl;
	use Edde\Common\Html\Tag\ColumnControl;
	use Edde\Common\Html\Tag\ColumnGroupControl;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\Tag\ImgControl;
	use Edde\Common\Html\Tag\ParagraphControl;
	use Edde\Common\Html\Tag\SectionControl;
	use Edde\Common\Html\Tag\SpanControl;
	use Edde\Common\Html\Tag\TableBodyControl;
	use Edde\Common\Html\Tag\TableCellControl;
	use Edde\Common\Html\Tag\TableControl;
	use Edde\Common\Html\Tag\TableFootControl;
	use Edde\Common\Html\Tag\TableHeadControl;
	use Edde\Common\Html\Tag\TableHeaderControl;
	use Edde\Common\Html\Tag\TableRowControl;
	use Edde\Common\Object;
	use Edde\Common\TemplateEngine\HelperSet;
	use Edde\Common\TemplateEngine\Macro\BlockMacro;
	use Edde\Common\TemplateEngine\Macro\ImportMacro;
	use Edde\Common\TemplateEngine\Macro\IncludeMacro;
	use Edde\Common\TemplateEngine\MacroSet;

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
			$macroSet->setMacroList([
				$container->create(ImportMacro::class),
				$container->create(LoadMacro::class),
				$container->create(IncludeMacro::class),
				$container->create(LoopMacro::class),
				$container->create(IfMacro::class),
				$container->create(SwitchMacro::class),
				$container->create(CaseMacro::class),
				$container->create(UseMacro::class),
				$container->create(ControlMacro::class),
				$container->create(BlockMacro::class),
				$container->create(CallMacro::class),
				$container->create(CssMacro::class),
				$container->create(JsMacro::class),
				$container->create(SchemaMacro::class),
				$container->create(PropertyMacro::class),
				$container->create(PassMacro::class),
				$container->create(PassChildMacro::class),
				$container->create(SnippetMacro::class),
				$container->create(TranslatorMacro::class),
				$container->create(DictionaryMacro::class),
				$container->create(HtmlMacro::class, [
					'div',
					DivControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'span',
					SpanControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'p',
					ParagraphControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'img',
					ImgControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'text',
					TextControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'password',
					PasswordControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'placeholder',
					PlaceholderControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'table',
					TableControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'thead',
					TableHeadControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'tbody',
					TableBodyControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'tfoot',
					TableFootControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'td',
					TableCellControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'tr',
					TableRowControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'th',
					TableHeaderControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'caption',
					CaptionControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'col',
					ColumnControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'colgroup',
					ColumnGroupControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'blockquote',
					BlockquoteControl::class,
				]),
				$container->create(HtmlMacro::class, [
					'section',
					SectionControl::class,
				]),
				$container->create(HeaderMacro::class, ['h1',]),
				$container->create(HeaderMacro::class, ['h2',]),
				$container->create(HeaderMacro::class, ['h3',]),
				$container->create(HeaderMacro::class, ['h4',]),
				$container->create(HeaderMacro::class, ['h5',]),
				$container->create(HeaderMacro::class, ['h6',]),
				$container->create(ButtonMacro::class),
				$container->create(FillMacro::class),
				$container->create(TitleMacro::class),
				$container->create(AttrMacro::class),
			]);
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
