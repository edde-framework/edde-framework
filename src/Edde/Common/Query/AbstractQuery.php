<?php
	namespace Edde\Common\Query;

	use Edde\Api\Node\INode;
	use Edde\Api\Query\IQuery;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractQuery extends AbstractUsable implements IQuery {
		/**
		 * @var INode
		 */
		protected $node;

		public function getNode() {
			$this->usse();
			return $this->node;
		}

		public function optimize() {
			return $this;
		}
	}
