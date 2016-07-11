<?php
	namespace Edde\Common\Query\Insert;

	use Edde\Api\Schema\ISchema;
	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractQuery;

	class InsertQuery extends AbstractQuery {
		/**
		 * @var ISchema
		 */
		protected $schema;
		/**
		 * @var array
		 */
		protected $insert;

		/**
		 * @param ISchema $schema
		 * @param array $insert
		 */
		public function __construct(ISchema $schema, array $insert) {
			$this->schema = $schema;
			$this->insert = $insert;
		}

		protected function prepare() {
			$this->node = new Node('insert-query', $this->schema->getSchemaName());
			foreach ($this->insert as $name => $value) {
				$this->node->addNode(new Node($name, $value));
			}
		}
	}
