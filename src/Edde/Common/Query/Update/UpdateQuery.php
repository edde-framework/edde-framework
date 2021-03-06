<?php
	declare(strict_types=1);

	namespace Edde\Common\Query\Update;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractQuery;
	use Edde\Common\Query\Where\WhereExpressionFragment;

	class UpdateQuery extends AbstractQuery implements IConfigurable {
		use ConfigurableTrait;
		/**
		 * @var ISchema
		 */
		protected $schema;
		/**
		 * @var array
		 */
		protected $update;
		/**
		 * @var WhereExpressionFragment
		 */
		protected $whereExpressionFragment;

		/**
		 * @param ISchema $schema
		 * @param array   $update
		 */
		public function __construct(ISchema $schema, array $update) {
			$this->schema = $schema;
			$this->update = $update;
		}

		/**
		 * @return WhereExpressionFragment
		 */
		public function where(): WhereExpressionFragment {
			return $this->whereExpressionFragment;
		}

		protected function handleInit() {
			parent::handleInit();
			$this->node = new Node('update-query', $this->schema->getSchemaName());
			$this->node->addNodeList([
				$updateNode = new Node('update'),
				$whereNode = new Node('where'),
			]);
			foreach ($this->update as $name => $value) {
				$updateNode->addNode(new Node($name, $value));
			}
			$this->whereExpressionFragment = new WhereExpressionFragment($whereNode);
		}
	}
