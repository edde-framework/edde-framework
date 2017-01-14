<?php
	declare(strict_types = 1);

	namespace Edde\Common\Query;

	use Edde\Api\Node\INode;
	use Edde\Api\Query\IQuery;
	use Edde\Common\Object;

	abstract class AbstractQuery extends Object implements IQuery {
		/**
		 * @var INode
		 */
		protected $node;

		public function getNode() {
			return $this->node;
		}

		public function optimize() {
			return $this;
		}
	}
