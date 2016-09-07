<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

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
			switch ($macro->getName()) {
				case 'schema':
					$this->checkLeaf($macro, $element);
					$this->checkAttribute($macro, $element, 'name', 'schema');
					$this->schemaList[$macro->getAttribute('name')] = $macro->getAttribute('schema');
					$this->lambda($macro, $element, $compiler);
					break;
				case 'property':
					$this->checkValue($macro, $element);
					list($schema, $property) = explode('.', $macro->getValue());
					if (isset($this->schemaList[$schema]) === false) {
						throw new MacroException(sprintf('Unknown attribute schema [%s] on [%s].', $schema, $element->getPath()));
					}
					$element->setAttribute('data-schema', $this->schemaList[$schema]);
					$element->setAttribute('data-property', $property);
					$this->lambda($macro, $element, $compiler);
					break;
			}
		}

		public function __clone() {
			$this->schemaList = [];
		}
	}
