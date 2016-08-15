<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Template\AbstractMacro;

	abstract class AbstractControlMacro extends AbstractMacro {
		/**
		 * @var string
		 */
		protected $control;

		/**
		 * @param array $macroList
		 * @param $control
		 */
		public function __construct(array $macroList, string $control) {
			parent::__construct($macroList);
			$this->control = $control;
		}

		public function run(ITemplateManager $templateManager, ITemplate $template, INode $root, IFile $file, ...$parameterList) {
			$file = $template->getFile();
			$file->write("\t\t\t\$parent = \$this->stack->top();\n");
			$file->write(sprintf("\t\t\t\$parent->addControl(\$control = \$this->container->create('%s'));\n", $this->control));
			if (($attributeList = $this->getAttributeList($root)) !== []) {
				$file->write(sprintf("\t\t\t\$control->setAttributeList(%s);\n", var_export($attributeList, true)));
			}
			$this->macro($root, $templateManager, $template, $file);
		}

		protected function getAttributeList(INode $node) {
			return $node->getAttributeList();
		}

		protected function macro(INode $root, ITemplateManager $templateManager, ITemplate $template, IFile $file, ...$parameterList) {
			$templateFile = $template->getFile();
			if ($root->isLeaf()) {
				parent::macro($root, $templateManager, $template, $file);
				return;
			}
			$templateFile->write("\t\t\t\$this->stack->push(\$control);\n");
			parent::macro($root, $templateManager, $template, $file);
			$templateFile->write("\t\t\t\$this->stack->pop();\n");
		}
	}
