<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\Template\AbstractMacro;

	class SchemaMacro extends AbstractMacro {
		/**
		 * @var ISchemaManager
		 */
		protected $schemaMAnager;
		/**
		 * @var string[]
		 */
		protected $schemaList = [];

		public function __construct() {
			parent::__construct(['schema']);
		}

		public function getSchema($name) {
			if (isset($this->schemaList[$name]) === false) {
				throw new TemplateException(sprintf('Unknown schema [%s]; did you used "schema" node?', $name));
			}
			return $this->schemaList[$name];
		}

		public function run(ITemplate $template, INode $root, ...$parameterList) {
			$this->schemaList[$root->getAttribute('name')] = $root->getAttribute('schema');
		}
	}
