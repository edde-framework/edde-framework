<?php
	declare(strict_types=1);

	namespace Edde\Common\Query\Insert;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractQuery;

	/**
	 * IQL implementation of an insert query.
	 */
	class InsertQuery extends AbstractQuery implements IConfigurable {
		use ConfigurableTrait;
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
		 * @param array   $insert
		 */
		public function __construct(ISchema $schema, array $insert) {
			$this->schema = $schema;
			$this->insert = $insert;
		}

		/**
		 * @inheritdoc
		 */
		protected function handleInit() {
			parent::handleInit();
			$this->node = new Node('insert-query', $this->schema->getSchemaName());
			foreach ($this->insert as $name => $value) {
				$this->node->addNode(new Node($name, $value));
			}
		}
	}
