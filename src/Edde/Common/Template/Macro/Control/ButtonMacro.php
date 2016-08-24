<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Html\ButtonControl;

	class ButtonMacro extends AbstractControlMacro {
		public function __construct() {
			parent::__construct(['button'], ButtonControl::class);
		}

		public function run(INode $root, ICompiler $compiler, callable $callback = null) {
			$destination = $compiler->getDestination();
			$destination->write("\t\t\t\$parent = \$this->stack->top();\n");
			$destination->write(sprintf("\t\t\t\$parent->addControl(\$control = \$this->container->create('%s'));\n", $this->control));
			$attributeList = $this->getAttributeList($root, $compiler);
			if (isset($attributeList['action']) === false) {
				throw new MacroException(sprintf('Missing mandatory attribute "action" in [%s].', $root->getPath()));
			}
			$action = $root->getAttribute('action');
			unset($attributeList['action']);
			$destination->write(sprintf("\t\t\t\$control->setAction([\$this->proxy, %s]);\n", $compiler->value($action)));
			$this->writeAttributeList($attributeList, $destination);
			$this->macro($root, $compiler, $callback);
		}
	}
