<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Html\DivControl;
	use Edde\Common\Template\AbstractMacro;

	class DivNodeMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct([
				'div',
			]);
		}

		public function run(ITemplateManager $templateManager, ITemplate $template, INode $root, IFile $file, ...$parameterList) {
			$file = $template->getFile();
			$file->write(sprintf("\t\t\t\$parent = \$this->stack->top();\n", DivControl::class));
			$file->write(sprintf("\t\t\t\$parent->addControl(\$control = \$this->container->create('%s'));\n", DivControl::class));
			if (($attributeList = $root->getAttributeList()) !== []) {
				$file->write(sprintf("\t\t\t\$control->setAttributeList(%s);\n", var_export($attributeList, true)));
			}
			$this->macro($root, $templateManager, $template, $file);
		}

		protected function macro(INode $root, ITemplateManager $templateManager, ITemplate $template, IResource $resource, ...$parameterList) {
			$file = $template->getFile();
			if ($root->isLeaf()) {
				parent::macro($root, $templateManager, $template, $resource);
				return;
			}
			$file->write("\t\t\t\$this->stack->push(\$control);\n");
			parent::macro($root, $templateManager, $template, $resource);
			$file->write("\t\t\t\$this->stack->pop();\n");
		}
	}
