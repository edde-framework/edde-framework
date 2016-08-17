<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Html\HeaderControl;

	class HeaderMacro extends AbstractControlMacro {
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

		public function run(INode $root, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			$destination->write("\t\t\t\$parent = \$this->stack->top();\n");
			$destination->write(sprintf("\t\t\t\$parent->addControl(\$control = \$this->container->create('%s'));\n", HeaderControl::class));
			$destination->write(sprintf("\t\t\t\$control->setTag('%s');\n", $root->getName()));
			$this->writeTextValue($root, $destination, $compiler);
			$this->writeAttributeList($this->getAttributeList($root, $compiler), $destination);
			$this->macro($root, $compiler);
		}
	}
