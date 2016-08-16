<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\AbstractMacro;

	class SwitchMacro extends AbstractMacro {
		/**
		 * @var ICryptEngine
		 */
		protected $cryptEngine;
		/**
		 * @var \SplStack
		 */
		protected $stack;

		public function __construct(ICryptEngine $cryptEngine) {
			parent::__construct([
				'm:switch',
				'm:case',
			]);
			$this->cryptEngine = $cryptEngine;
			$this->stack = new \SplStack();
		}

		public function run(ITemplateManager $templateManager, ITemplate $template, INode $root, IFile $file, ...$parameterList) {
			$file = $template->getFile();
			switch ($root->getName()) {
				case 'm:switch':
					$this->stack->push($id = StringUtils::camelize($this->cryptEngine->guid()));
					$file->write(sprintf("\t\t\t\$_%s = %s;\n", $id, $this->value($root->getValue())));
					$this->macro($root, $templateManager, $template, $file, ...$parameterList);
					break;
				case 'm:case':
					$file->write(sprintf("\t\t\tif(\$_%s === %s) {\n", $this->stack->top(), $this->value($root->getValue())));
					$this->macro($root, $templateManager, $template, $file, ...$parameterList);
					$file->write("\t\t\t}\n");
					break;
			}
		}
	}
