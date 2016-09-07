<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Control\IControl;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;

	class SchemaMacro extends AbstractHtmlMacro {
		/**
		 * @var array
		 */
		protected $schemaList = [];

		public function __construct() {
			parent::__construct([
				'schema',
				'property',
			]);
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			switch ($macro->getName()) {
				case 'schema':
					$this->checkLeaf($macro, $element);
					$this->checkAttribute($macro, $element, 'name', 'schema');
					$this->schemaList[$macro->getAttribute('name')] = $macro->getAttribute('schema');
					$destination->write(sprintf("\t\t\t/** %s (%s) */\n", $macro->getPath(), $element->getPath()));
					$destination->write(sprintf("\t\t\t\$controlList[%s] = function(%s \$root) use(&\$controlList, &\$stash) {\n", $compiler->delimite($macro->getMeta('control')), IControl::class));
					foreach ($macro->getNodeList() as $node) {
						$destination->write(sprintf("\t\t\t\t/** %s */\n", $node->getPath()));
						$destination->write(sprintf("\t\t\t\t\$controlList[%s](\$root);\n", $compiler->delimite($node->getMeta('control'))));
					}
					$destination->write("\t\t\t};\n");
					$this->element($macro, $compiler);
					break;
				case 'property':
					$this->checkValue($macro, $element);
					list($schema, $property) = explode('.', $macro->getValue());
					if (isset($this->schemaList[$schema]) === false) {
						throw new MacroException(sprintf('Unknown attribute schema [%s] on [%s].', $schema, $element->getPath()));
					}
					$element->setAttribute('data-schema', $this->schemaList[$schema]);
					$element->setAttribute('data-property', $property);
					$destination->write(sprintf("\t\t\t/** %s (%s) */\n", $macro->getPath(), $element->getPath()));
					$destination->write(sprintf("\t\t\t\$controlList[%s] = function(%s \$root) use(&\$controlList, &\$stash) {\n", $compiler->delimite($macro->getMeta('control')), IControl::class));
					foreach ($macro->getNodeList() as $node) {
						$destination->write(sprintf("\t\t\t\t/** %s */\n", $node->getPath()));
						$destination->write(sprintf("\t\t\t\t\$controlList[%s](\$root);\n", $compiler->delimite($node->getMeta('control'))));
					}
					$destination->write("\t\t\t};\n");
					$this->element($macro, $compiler);
					break;
			}
		}

		public function __clone() {
			$this->schemaList = [];
		}
	}
