<?php
	declare(strict_types = 1);

	namespace Edde\Common\Query\Select;

	use Edde\Api\Node\INode;
	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractFragment;

	class WhereExpressionFragment extends AbstractFragment {
		/**
		 * @var SelectQuery
		 */
		protected $selectQuery;
		protected $relation = 'and';

		/**
		 * @param INode $whereNode
		 * @param SelectQuery $selectQuery
		 */
		public function __construct(INode $whereNode, SelectQuery $selectQuery) {
			parent::__construct($whereNode);
			$this->selectQuery = $selectQuery;
		}

		/**
		 * @return WhereFragment
		 */
		public function eq() {
			return $this->createWhereFragment('equal');
		}

		protected function createWhereFragment($name) {
			$this->node->addNode($node = new Node($name, null, [
				'relation' => $this->relation,
			]));
			return new WhereFragment($node, $this, $this->selectQuery);
		}

		/**
		 * @return WhereFragment
		 */
		public function neq() {
			return $this->createWhereFragment('not-equal');
		}

		/**
		 * @return WhereFragment
		 */
		public function isNull() {
			return $this->createWhereFragment('is-null');
		}

		/**
		 * @return WhereFragment
		 */
		public function isNotNull() {
			return $this->createWhereFragment('is-not-null');
		}

		/**
		 * @return WhereFragment
		 */
		public function gt() {
			return $this->createWhereFragment('greater-than');
		}

		/**
		 * @return WhereFragment
		 */
		public function gte() {
			return $this->createWhereFragment('greater-than-equal');
		}

		/**
		 * @return WhereFragment
		 */
		public function lt() {
			return $this->createWhereFragment('lesser-than');
		}

		/**
		 * @return WhereFragment
		 */
		public function lte() {
			return $this->createWhereFragment('lesser-than-equal');
		}

		/**
		 * @return WhereExpressionFragment
		 */
		public function and () {
			$this->relation = 'and';
			return $this;
		}

		/**
		 * @return WhereExpressionFragment
		 */
		public function or () {
			$this->relation = 'or';
			return $this;
		}

		/**
		 * @return WhereFragment
		 */
		public function like() {
			return $this->createWhereFragment('like');
		}

		/**
		 * @return WhereExpressionFragment
		 */
		public function group() {
			$this->node->addNode($groupNode = new Node('where-group', null, [
				'relation' => $this->relation,
			]));
			return new self($groupNode, $this->selectQuery);
		}
	}
