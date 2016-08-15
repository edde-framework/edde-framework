<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Template\AbstractMacro;

	class SchemaNodeMacro extends AbstractMacro {
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

		public function run(ITemplateManager $templateManager, ITemplate $template, INode $root, IFile $file, ...$parameterList) {
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
					$this->macro($root, $templateManager, $template, $file, ...$parameterList);
					break;
			}
		}
	}
