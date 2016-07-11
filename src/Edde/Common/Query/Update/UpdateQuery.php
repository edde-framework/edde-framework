<?php
	namespace Edde\Common\Query\Update;

	use Edde\Api\Schema\ISchema;
	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractQuery;

	class UpdateQuery extends AbstractQuery {
		/**
		 * @var ISchema
		 */
		protected $schema;
		/**
		 * @var array
		 */
		protected $update;

		/**
		 * @param ISchema $schema
		 * @param array $update
		 */
		public function __construct(ISchema $schema, array $update) {
			$this->schema = $schema;
			$this->update = $update;
		}

		protected function prepare() {
			$this->node = new Node('update-query', $this->schema->getSchemaName());
			foreach ($this->update as $name => $value) {
				$this->node->addNode(new Node($name, $value));
			}
			throw new \Exception('not implemented yet: Where filtering!');
		}
	}
