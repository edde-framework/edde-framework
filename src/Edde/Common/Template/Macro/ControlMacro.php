<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Template\AbstractMacro;

	class ControlMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct(['control']);
		}

		public function run(ITemplateManager $templateManager, ITemplate $template, INode $root, IResource $resource, ...$parameterList) {
			$file = $template->getFile();
			$file->write("\t\tprotected \$container;\n\n");
			$file->write("\t\tprotected \$stack;\n\n");
			$file->write("\t\tprotected \$proxy;\n\n");
			$file->write(sprintf("\t\tpublic function __construct(%s \$container) {\n", IContainer::class));
			$file->write("\t\t\t\$this->container = \$container;\n");
			$file->write(sprintf("\t\t\t\$this->stack = new %s;\n", \SplStack::class));
			$file->write("\t\t}\n\n");
			$file->write("\t\tpublic function __call(\$function, array \$parameterList) {\n");
			$file->write("\t\t\treturn call_user_func_array([\$this->proxy, \$function], \$parameterList);\n");
			$file->write("\t\t}\n\n");
			$file->write(sprintf("\t\tpublic function template(\\%s \$parent) {\n", IHtmlControl::class));
			$file->write("\t\t\t\$this->proxy = \$parent;\n");
			if (($attributeList = $root->getAttributeList()) !== []) {
				$file->write(sprintf("\t\t\t\$parent->setAttributeList(%s);\n", var_export($attributeList, true)));
			}
			$file->write("\t\t\t\$this->stack->push(\$parent);\n");
			foreach ($root->getNodeList() as $node) {
				$templateManager->macro($node, $template, $resource, ...$parameterList);
			}
			$file->write("\t\t}\n");
		}
	}
