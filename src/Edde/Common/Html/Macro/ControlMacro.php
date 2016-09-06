<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Control\IControl;
	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Html\Input\PasswordControl;
	use Edde\Common\Html\Input\TextControl;
	use Edde\Common\Html\PlaceholderControl;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\Tag\ImgControl;
	use Edde\Common\Html\Tag\SpanControl;
	use Edde\Common\Template\AbstractMacro;

	class ControlMacro extends AbstractMacro {
		/**
		 * @var string
		 */
		protected $control;

		/**
		 * @param array $macroList
		 * @param $control
		 */
		public function __construct($macroList, string $control) {
			parent::__construct((array)$macroList);
			$this->control = $control;
		}

		public static function macroList(IContainer $container): array {
			return [
				MacroSet::controlMacro(),
				MacroSet::snippetMacro(),
				MacroSet::passMacro(),
				MacroSet::schemaMacro(),
				MacroSet::jsMacro(),
				MacroSet::cssMacro(),
				MacroSet::buttonMacro(),
				MacroSet::headerMacro(),
				MacroSet::layoutMacro(),
				$container->inject(MacroSet::bindMacro()),
				$container->inject(new IncludeMacro()),
				$container->inject(new SwitchMacro()),
				$container->inject(new LoopMacro()),
				new ControlMacro('div', DivControl::class),
				new ControlMacro('span', SpanControl::class),
				new ControlMacro('text', TextControl::class),
				new ControlMacro('password', PasswordControl::class),
				new ControlMacro('img', ImgControl::class),
				new ControlMacro('placeholder', PlaceholderControl::class),
			];
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			$destination->write(sprintf("\t\t\t\t/** %s */\n", $element->getPath()));

			$destination->write(sprintf("\t\t\t\$controlList[%s] = \$func = function(%s \$root) use(&\$controlList) {\n", $id = $compiler->delimite($element->getAttribute('id', hash('sha256', random_bytes(256)))), IControl::class));
			$destination->write("\t\t\t\t\$stack = new SplStack();\n");
			$destination->write("\t\t\t\t\$stack->push(\$parent = \$root);\n");

			$destination->write("\t\t\t\t\$parent = \$stack->top();\n");
			$destination->write(sprintf("\t\t\t\t\$parent->addControl(\$control = \$this->container->create(%s));\n", $compiler->delimite($this->control)));
			$this->writeTextValue($element, $destination, $compiler);
			$attributeList = $this->getAttributeList($element, $compiler);
			unset($attributeList['value']);
			$this->writeAttributeList($attributeList, $destination);
			$this->element($element, $compiler);
			$destination->write("\t\t\t};\n");
			$destination->write("\t\t\t\$func(\$stack->top());\n");
		}

		protected function writeTextValue(INode $root, IFile $destination, ICompiler $compiler) {
			if ($root->isLeaf() && ($text = $root->getValue($root->getAttribute('value'))) !== null) {
				$destination->write(sprintf("\t\t\t\t\$control->setText(%s);\n", $compiler->delimite($text)));
			}
		}

		protected function writeAttributeList(array $attributeList, IFile $destination) {
			if (empty($attributeList) === false) {
				$export = [];
				foreach ($attributeList as $name => $value) {
					$export[] = "'" . $name . "' => " . $value;
				}
				$destination->write(sprintf("\t\t\t\t\$control->setAttributeList([%s]);\n", implode(",\n", $export)));
			}
		}

		protected function element(INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			if ($element->isLeaf()) {
				parent::element($element, $compiler);
				return;
			}
			$destination->write("\t\t\t\t\$stack->push(\$control);\n");
			parent::element($element, $compiler);
//			if ($element->getLevel() > 1 && $element->isLast() === false) {
			$destination->write("\t\t\t\t\$control = \$stack->pop();\n");
//			}
		}
	}
