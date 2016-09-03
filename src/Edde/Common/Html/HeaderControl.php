<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Common\Template\Macro\Control\ControlMacro;

	class HeaderControl extends AbstractHtmlControl {
		static public function macro(): IMacro {
			return new class extends ControlMacro {
				public function __construct() {
					parent::__construct([
						'h1',
						'h2',
						'h3',
						'h4',
						'h5',
						'h6',
					], '');
				}

				public function run(INode $root, ICompiler $compiler, callable $callback = null) {
					$destination = $compiler->getDestination();
					$destination->write("\t\t\t\$parent = \$this->stack->top();\n");
					$destination->write(sprintf("\t\t\t\$parent->addControl(\$control = \$this->container->create('%s'));\n", HeaderControl::class));
					$destination->write(sprintf("\t\t\t\$control->setTag('%s');\n", $root->getName()));
					$this->writeTextValue($root, $destination, $compiler);
					$this->writeAttributeList($this->getAttributeList($root, $compiler), $destination);
					$this->macro($root, $compiler, $callback);
				}
			};
		}
	}
