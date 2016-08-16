<?php
	declare(strict_types = 1);

	namespace Edde\Common\Query\Select;

	use Edde\Api\Node\INode;
	use Edde\Api\Query\QueryException;
	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractQuery;

	class SelectQuery extends AbstractQuery {
		/**
		 * @var INode
		 */
		protected $selectNode;
		/**
		 * @var SelectFragment
		 */
		protected $selectPropertyFragment;
		/**
		 * @var FromFragment
		 */
		protected $fromPropertyFragment;
		/**
		 * @var WhereExpressionFragment
		 */
		protected $whereExpressionFragment;

		/**
		 * @return SelectFragment
		 */
		public function select() {
			$this->use();
			return $this->selectPropertyFragment;
		}

		/**
		 * @return FromFragment
		 */
		public function from() {
			$this->use();
			return $this->fromPropertyFragment;
		}

		/**
		 * @return WhereExpressionFragment
		 */
		public function where() {
			$this->use();
			return $this->whereExpressionFragment;
		}

		public function getNode() {
			if ($this->selectNode === null) {
				throw new QueryException(sprintf('Empty select query has no sense; please start with %s::select() method.', self::class));
			}
			return $this->selectNode;
		}

		protected function prepare() {
			$this->selectNode = new Node('select-query');
			$this->selectNode->addNodeList([
				$selectListNode = new Node('select'),
				$fromListNode = new Node('from'),
				$whereNode = new Node('where'),
			]);
			$this->selectPropertyFragment = new SelectFragment($selectListNode, $this);
			$this->fromPropertyFragment = new FromFragment($fromListNode, $this);
			$this->whereExpressionFragment = new WhereExpressionFragment($whereNode, $this);
		}
	}
