<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Template\AbstractMacro;

	class LoopMacro extends AbstractMacro {
		/**
		 * @var \SplStack
		 */
		protected $variableStack;

		public function __construct() {
			parent::__construct([
				'loop',
				'm:loop',
			]);
			$this->variableStack = new \SplStack();
		}

		public function run(ITemplateManager $templateManager, ITemplate $template, INode $root, IFile $file, ...$parameterList) {
			$templateFile = $template->getFile();
			switch ($root->getName()) {
				case 'loop':
					$this->variableStack->push([

					]);
					$templateFile->write(sprintf("\t\t\tforeach(%s as $%s => $%s) {\n", $this->value($root->getAttribute('src')), 'a', 'b'));
					$this->macro($root, $templateManager, $template, $file);
					$templateFile->write("\t\t\t}\n");
					break;
				case 'm:loop':
					break;
			}
		}
	}
