<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Template\AbstractMacro;

	class BindIdAttributeMacro extends AbstractMacro {
		use LazyInjectTrait;

		protected $idList;
		/**
		 * @var ICryptEngine
		 */
		protected $cryptEngine;

		public function __construct() {
			parent::__construct([
				'm:id',
				'm:bind',
			]);
		}

		public function lazyCryptEngine(ICryptEngine $cryptEngine) {
			$this->cryptEngine = $cryptEngine;
		}

		public function run(INode $root, ICompiler $compiler) {
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
			$this->macro($root, $compiler);
		}

		public function __clone() {
			$this->idList = [];
		}
	}
