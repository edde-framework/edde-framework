<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Control\IControl;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Html\HtmlTemplate;
	use Edde\Common\Html\Input\PasswordControl;
	use Edde\Common\Html\Input\TextControl;
	use Edde\Common\Html\PlaceholderControl;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\Tag\ImgControl;
	use Edde\Common\Html\Tag\SpanControl;
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
				$container->inject(new ButtonMacro()),
				$container->inject(new StyleSheetMacro()),
				$container->inject(new JavaScriptMacro()),
				$container->inject(new SwitchMacro()),
				$container->inject(new SchemaMacro()),
				$container->inject(new BindMacro()),
				$container->inject(new IncludeMacro()),
				$container->inject(new LoopMacro()),
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
	 * source = %s
	 * date = %s	        
	 */\n", $source->getPath(), (new \DateTime())->format('Y-m-d H:i:s')));
					$destination->write(sprintf("\tclass %s extends %s {\n", $compiler->getName(), HtmlTemplate::class));
					$destination->write("\t\tprotected function onTemplate() {\n");
					$destination->write("\t\t\t\$controlList = [];\n");
					$destination->write("\t\t\t\$stash = [];\n");
					foreach (NodeIterator::recursive($element) as $node) {
						$node->setMeta('control', $id = $node->getAttribute('id', $this->cryptEngine->guid()));
					}
					$destination->write(sprintf("\t\t\t\$controlList[null][] = function(%s \$root) use(&\$controlList, &\$stash) {\n", IControl::class));
				$destination->write("\t\t\t\t\$control = \$root;\n");
					$this->writeAttributeList($this->getAttributeList($element, $compiler), $destination);
					foreach ($element->getNodeList() as $node) {
						$destination->write(sprintf("\t\t\t\t/** %s */\n", $node->getPath()));
						$destination->write(sprintf("\t\t\t\t\$controlList[%s](\$control);\n", $compiler->delimite($node->getMeta('control'))));
					}
					$destination->write("\t\t\t};\n");
					$this->element($element, $compiler);
					// fun will be here
					$destination->write("\t\t\treturn \$controlList;\n");
					$destination->write("\t\t}\n");
					$destination->write("\t}\n");
					break;
			}
		}
	}
