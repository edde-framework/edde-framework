<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Template\AbstractMacro;

	class BindIdAttributeMacro extends AbstractMacro {
		/**
		 * @var ICryptEngine
		 */
		protected $cryptEngine;
		protected $idList;

		/**
		 * @param ICryptEngine $cryptEngine
		 */
		public function __construct(ICryptEngine $cryptEngine) {
			parent::__construct([
				'm:id',
				'm:bind',
			]);
			$this->cryptEngine = $cryptEngine;
		}

		public function run(ITemplateManager $templateManager, ITemplate $template, INode $root, IFile $file, ...$parameterList) {
			if ($root->isLeaf()) {
				throw new MacroException(sprintf('Node [%s] must have children.', $root->getPath()));
			}
			$node = $root->getNodeList()[0];
			switch ($root->getName()) {
				case 'm:id':
					$node->setAttribute('id', $this->idList[$root->getValue()] = $node->getAttribute('id', $this->cryptEngine->guid()));
					break;
				case 'm:bind':
					if (isset($this->idList[$id = $root->getValue()]) === false) {
						throw new MacroException(sprintf('Unknown bind id [%s].', $id));
					}
					$node->setAttribute('bind', $this->idList[$id]);
					break;
			}
			$this->macro($root, $templateManager, $template, $file, ...$parameterList);
		}
	}
