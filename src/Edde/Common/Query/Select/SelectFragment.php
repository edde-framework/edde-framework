<?php
	namespace Edde\Common\Query\Select;

	use Edde\Api\Node\INode;
	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractFragment;

	class SelectFragment extends AbstractFragment {
		/**
		 * @var SelectQuery
		 */
		private $selectQuery;

		public function __construct(INode $node, SelectQuery $selectQuery) {
			parent::__construct($node);
			$this->selectQuery = $selectQuery;
		}

		/**
		 * select property of a schema; it's something similar to a column
		 *
		 * @param string $property
		 * @param string|null $prefix
		 * @param string|null $alias
		 *
		 * @return $this
		 */
		public function property($property, $prefix = null, $alias = null) {
			$this->node->addNode(new Node('property', $property, [
				'alias' => $alias,
				'prefix' => $prefix,
			]));
			return $this;
		}

		public function from() {
			return $this->selectQuery->from();
		}

		public function where() {
			return $this->selectQuery->where();
		}
	}
