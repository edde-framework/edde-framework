<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

	use Edde\Api\Container\IContainer;
	use Edde\Api\File\IFile;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Template\AbstractMacro;

	class ControlMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct(['control']);
		}

		public function run(ITemplateManager $templateManager, ITemplate $template, INode $root, IFile $file, ...$parameterList) {
			$templateFile = $template->getFile();
			$templateFile->write("\t\tprotected \$container;\n\n");
			$templateFile->write("\t\tprotected \$styleSheetCompiler;\n\n");
			$templateFile->write("\t\tprotected \$javaScriptCompiler;\n\n");
			$templateFile->write("\t\tprotected \$stack;\n\n");
			$templateFile->write("\t\tprotected \$proxy;\n\n");
			$templateFile->write(sprintf("\t\tpublic function __construct(%s \$container, %s \$styleSheetCompiler, %s \$javaScriptCompiler) {\n", IContainer::class, IStyleSheetCompiler::class, IJavaScriptCompiler::class));
			$templateFile->write("\t\t\t\$this->container = \$container;\n");
			$templateFile->write("\t\t\t\$this->styleSheetCompiler = \$styleSheetCompiler;\n");
			$templateFile->write("\t\t\t\$this->javaScriptCompiler = \$javaScriptCompiler;\n");
			$templateFile->write(sprintf("\t\t\t\$this->stack = new %s;\n", \SplStack::class));
			$templateFile->write("\t\t}\n\n");
			$templateFile->write("\t\tpublic function __call(\$function, array \$parameterList) {\n");
			$templateFile->write("\t\t\treturn call_user_func_array([\$this->proxy, \$function], \$parameterList);\n");
			$templateFile->write("\t\t}\n\n");
			$templateFile->write(sprintf("\t\tpublic function template(\\%s \$parent) {\n", IHtmlControl::class));
			$templateFile->write("\t\t\t\$this->proxy = \$parent;\n");
			if (($attributeList = $root->getAttributeList()) !== []) {
				$templateFile->write(sprintf("\t\t\t\$parent->setAttributeList(%s);\n", var_export($attributeList, true)));
			}
			$templateFile->write("\t\t\t\$this->stack->push(\$parent);\n");
			foreach ($root->getNodeList() as $node) {
				$templateManager->macro($node, $template, $file, ...$parameterList);
			}
			$templateFile->write("\t\t}\n");
		}
	}
