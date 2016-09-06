<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Control\IControl;
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
				'property',
			]);
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			switch ($macro->getName()) {
				case 'schema':
					$this->checkAttribute($macro, $element, 'name', 'schema');
					$this->schemaList[$macro->getAttribute('name')] = $macro->getAttribute('schema');
					$destination->write(sprintf("\t\t\t/** %s */\n", $element->getPath()));
					$destination->write(sprintf("\t\t\t\$controlList[%s] = function(%s \$root) use(&\$controlList, &\$stash) {\n", $compiler->delimite($element->getMeta('control')), IControl::class));
					foreach ($element->getNodeList() as $node) {
						$destination->write(sprintf("\t\t\t\t/** %s */\n", $node->getPath()));
						$destination->write(sprintf("\t\t\t\t\$controlList[%s](\$root);\n", $compiler->delimite($node->getMeta('control'))));
					}
					$destination->write("\t\t\t};\n");
					break;
				case 'property':
					$this->checkValue($macro, $element);
					list($schema, $property) = explode('.', $macro->getValue());
					if (isset($this->schemaList[$schema]) === false) {
						throw new MacroException(sprintf('Unknown attribute schema [%s] on [%s].', $schema, $element->getPath()));
					}
					$e = $element->getNodeList()[0];
					$e->setAttribute('data-schema', $this->schemaList[$schema]);
					$e->setAttribute('data-property', $property);
					$this->element($element, $compiler);
					$destination->write(sprintf("\t\t\t/** %s */\n", $element->getPath()));
					$destination->write(sprintf("\t\t\t\$controlList[%s] = function(%s \$root) use(&\$controlList, &\$stash) {\n", $compiler->delimite($element->getMeta('control')), IControl::class));
					foreach ($element->getNodeList() as $node) {
						$destination->write(sprintf("\t\t\t\t/** %s */\n", $node->getPath()));
						$destination->write(sprintf("\t\t\t\t\$controlList[%s](\$root);\n", $compiler->delimite($node->getMeta('control'))));
					}
					$destination->write("\t\t\t};\n");
					break;
			}
		}

		public function __clone() {
			$this->schemaList = [];
		}
	}
