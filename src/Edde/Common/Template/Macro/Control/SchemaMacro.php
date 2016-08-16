<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Template\AbstractMacro;

	class SchemaMacro extends AbstractMacro {
		/**
		 * @var array
		 */
		protected $schemaList = [];

		public function __construct() {
			parent::__construct([
				'schema',
				'm:schema',
			]);
		}

		public function run(INode $root, ICompiler $compiler) {
			switch ($root->getName()) {
				case 'schema':
					$this->schemaList[$root->getAttribute('name')] = $root->getAttribute('schema');
					break;
				case 'm:schema':
					$attribute = explode('.', $root->getValue());
					if (isset($this->schemaList[$attribute[0]]) === false) {
						throw new MacroException(sprintf('Unknown attribute schema [%s] on [%s].', $attribute[0], $root->getPath()));
					}
					$node = $root->getNodeList()[0];
					$node->setAttribute('data-schema', $this->schemaList[$attribute[0]]);
					$node->setAttribute('data-property', $attribute[1]);
					$this->macro($root, $compiler);
					break;
			}
		}

		public function __clone() {
			$this->schemaList = [];
		}
	}
