<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\AbstractMacro;

	class SwitchMacro extends AbstractMacro {
		/**
		 * @var \SplStack
		 */
		protected $stack;

		public function __construct() {
			parent::__construct([
				'm:switch',
				'm:case',
			]);
			$this->stack = new \SplStack();
		}

		public function run(ITemplateManager $templateManager, ITemplate $template, INode $root, IResource $resource, ...$parameterList) {
			switch ($root->getName()) {
				case 'm:switch':
					$this->stack->push($root);
					$this->macro($root, $templateManager, $template, $resource, ...$parameterList);
					break;
				case 'm:case':
					/** @var $switchNode INode */
					$switchNode = $this->stack->top();
					$file = $template->getFile();
					$file->write(sprintf("\t\t\tif(%s === %s) {\n", $this->value($switchNode->getValue()), $this->value($root->getValue())));
					$this->macro($root, $templateManager, $template, $resource, ...$parameterList);
					$file->write("\t\t\t}\n");
					break;
			}
		}

		protected function value($value) {
			if (strpos($value, '()') !== false) {
				return '$this->' . StringUtils::firstLower(StringUtils::camelize($value));
			}
			return "'$value'";
		}
	}
