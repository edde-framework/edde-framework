<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Control\IControl;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Html\AbstractHtmlTemplate;
	use Edde\Common\Html\Input\PasswordControl;
	use Edde\Common\Html\Input\TextControl;
	use Edde\Common\Html\PlaceholderControl;
	use Edde\Common\Html\Tag\CaptionControl;
	use Edde\Common\Html\Tag\ColumnControl;
	use Edde\Common\Html\Tag\ColumnGroupControl;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\Tag\ImgControl;
	use Edde\Common\Html\Tag\SpanControl;
	use Edde\Common\Html\Tag\TableBodyControl;
	use Edde\Common\Html\Tag\TableCellControl;
	use Edde\Common\Html\Tag\TableControl;
	use Edde\Common\Html\Tag\TableFootControl;
	use Edde\Common\Html\Tag\TableHeadControl;
	use Edde\Common\Html\Tag\TableHeaderControl;
	use Edde\Common\Html\Tag\TableRowControl;
	use Edde\Common\Node\NodeIterator;

	class TemplateMacro extends ControlMacro {
		use LazyInjectTrait;
		/**
		 * @var ICryptEngine
		 */
		protected $cryptEngine;

		public function __construct() {
			parent::__construct([
				'control',
				'template',
			], '');
		}

		public static function macroList(IContainer $container): array {
			return [
				$container->inject(new TemplateMacro()),
				$container->inject(new ControlMacro('div', DivControl::class)),
				$container->inject(new ControlMacro('span', SpanControl::class)),
				$container->inject(new ControlMacro('text', TextControl::class)),
				$container->inject(new ControlMacro('password', PasswordControl::class)),
				$container->inject(new ControlMacro('img', ImgControl::class)),
				$container->inject(new ControlMacro('placeholder', PlaceholderControl::class)),
				$container->inject(new ControlMacro('table', TableControl::class)),
				$container->inject(new ControlMacro('thead', TableHeadControl::class)),
				$container->inject(new ControlMacro('tbody', TableBodyControl::class)),
				$container->inject(new ControlMacro('tfoot', TableFootControl::class)),
				$container->inject(new ControlMacro('tr', TableRowControl::class)),
				$container->inject(new ControlMacro('th', TableHeaderControl::class)),
				$container->inject(new ControlMacro('td', TableCellControl::class)),
				$container->inject(new ControlMacro('col', ColumnControl::class)),
				$container->inject(new ControlMacro('colgroup', ColumnGroupControl::class)),
				$container->inject(new ControlMacro('caption', CaptionControl::class)),
				$container->inject(new ButtonMacro()),
				$container->inject(new StyleSheetMacro()),
				$container->inject(new JavaScriptMacro()),
				$container->inject(new SwitchMacro()),
				$container->inject(new SchemaMacro()),
				$container->inject(new LoopMacro()),
				$container->inject(new HeaderMacro()),
				$container->inject(new PassMacro()),
				$container->inject(new IncludeMacro()),
				$container->inject(new SnippetMacro()),
			];
		}

		public function lazyCryptEngine(ICryptEngine $cryptEngine) {
			$this->cryptEngine = $cryptEngine;
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			$source = $compiler->getSource();
			switch ($macro->getName()) {
				case 'template':
				case 'control':
					$destination->write("<?php\n");
					$destination->write("\tdeclare(strict_types = 1);\n\n");
					$destination->write(sprintf("\t
	/**
	 * @automagically-generated file
	 *
	 * source = %s
	 * date = %s	        
	 */\n", $source->getPath(), (new \DateTime())->format('Y-m-d H:i:s')));
					$destination->write(sprintf("\tclass %s extends %s {\n", $compiler->getName(), AbstractHtmlTemplate::class));
					$destination->write("\t\tprotected function onPrepare() {\n");
					foreach (NodeIterator::recursive($element) as $node) {
						$node->setMeta('control', $this->cryptEngine->guid());
					}
					$destination->write(sprintf("\t\t\t\$this->controlList[null] = function(%s \$root) {\n", IControl::class));
					$destination->write("\t\t\t\t\$control = \$root;\n");
					$this->writeAttributeList($this->getAttributeList($element, $compiler), $destination);
					$this->dependencies($macro, $compiler);
					$destination->write("\t\t\t};\n");
					$this->element($element, $compiler);
					$destination->write("\t\t}\n");
					$destination->write("\t}\n");
					break;
			}
		}
	}
