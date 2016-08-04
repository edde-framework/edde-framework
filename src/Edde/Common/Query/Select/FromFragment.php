<?php
	declare(strict_types = 1);

	namespace Edde\Common\Query\Select;

	use Edde\Api\Node\INode;
	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractFragment;
	use Edde\Common\Usable\UsableTrait;

	class FromFragment extends AbstractFragment {
		use UsableTrait;
		/**
		 * @var SelectQuery
		 */
		private $selectQuery;
		/**
		 * @var JoinFragment
		 */
		private $joinFragment;

		public function __construct(INode $node, SelectQuery $selectQuery) {
			parent::__construct($node);
			$this->selectQuery = $selectQuery;
		}

		/**
		 * @return SelectFragment
		 */
		public function select() {
			return $this->selectQuery->select();
		}

		/**
		 * @param string $source
		 * @param string|null $alias
		 *
		 * @return FromFragment
		 */
		public function source($source, $alias = null) {
			$this->node->addNode(new Node('source', $source, [
				'alias' => $alias,
			]));
			return $this;
		}

		/**
		 * @return JoinFragment
		 */
		public function join() {
			return $this->joinFragment;
		}

		public function where() {
			return $this->selectQuery->where();
		}

		protected function prepare() {
			$this->joinFragment = new JoinFragment($this->node);
		}
	}
