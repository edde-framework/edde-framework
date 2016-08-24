<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Template\AbstractMacro;

	/**
	 * Root control template macro.
	 */
	class TemplateMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct(['control']);
		}

		public function run(INode $root, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			$destination->write("\t\tprotected \$container;\n\n");
			$destination->write("\t\tprotected \$styleSheetCompiler;\n\n");
			$destination->write("\t\tprotected \$javaScriptCompiler;\n\n");
			$destination->write("\t\tprotected \$stack;\n\n");
			$destination->write("\t\tprotected \$proxy;\n\n");
			$destination->write(sprintf("\t\tpublic function __construct(%s \$container, %s \$styleSheetCompiler, %s \$javaScriptCompiler) {\n", IContainer::class, IStyleSheetCompiler::class, IJavaScriptCompiler::class));
			$destination->write("\t\t\t\$this->container = \$container;\n");
			$destination->write("\t\t\t\$this->styleSheetCompiler = \$styleSheetCompiler;\n");
			$destination->write("\t\t\t\$this->javaScriptCompiler = \$javaScriptCompiler;\n");
			$destination->write(sprintf("\t\t\t\$this->stack = new %s;\n", \SplStack::class));
			$destination->write("\t\t}\n\n");
			$destination->write("\t\tpublic function __call(\$function, array \$parameterList) {\n");
			$destination->write("\t\t\treturn call_user_func_array([\$this->proxy, \$function], \$parameterList);\n");
			$destination->write("\t\t}\n\n");
			$destination->write(sprintf("\t\tpublic function template(\\%s \$parent) {\n", IHtmlControl::class));
			$destination->write("\t\t\t\$this->proxy = \$parent;\n");
			if (($attributeList = $root->getAttributeList()) !== []) {
				$destination->write(sprintf("\t\t\t\$parent->setAttributeList(%s);\n", var_export($attributeList, true)));
			}
			$destination->write("\t\t\t\$this->stack->push(\$parent);\n");
			$this->macro($root, $compiler);
			$destination->write("\t\t}\n");
		}
	}
