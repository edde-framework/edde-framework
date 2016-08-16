<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Html\ButtonControl;
	use Edde\Common\Strings\StringUtils;

	class ButtonNodeMacro extends AbstractControlMacro {
		public function __construct() {
			parent::__construct(['button'], ButtonControl::class);
		}

		public function run(ITemplateManager $templateManager, ITemplate $template, INode $root, IFile $file, ...$parameterList) {
			$file = $template->getFile();
			$file->write("\t\t\t\$parent = \$this->stack->top();\n");
			$file->write(sprintf("\t\t\t\$parent->addControl(\$control = \$this->container->create('%s'));\n", $this->control));
			$attributeList = $this->getAttributeList($root);
			if (isset($attributeList['action']) === false) {
				throw new MacroException(sprintf('Missing mandatory attribute "action" in [%s].', $root->getPath()));
			}
			if ($attributeList !== []) {
				$action = $attributeList['action'];
				unset($attributeList['action']);
				if ($attributeList !== []) {
					$file->write(sprintf("\t\t\t\$control->setAttributeList(%s);\n", var_export($attributeList, true)));
				}
				$file->write(sprintf("\t\t\t\$control->setAction(get_class(\$this->proxy), '%s');\n", StringUtils::camelize($action)));
			}
			$this->macro($root, $templateManager, $template, $file);
		}
	}
