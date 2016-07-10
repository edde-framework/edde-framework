<?php
	namespace Edde\Common\Query;

	use Edde\Api\Query\IQuery;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractQuery extends AbstractUsable implements IQuery {
		public function optimize() {
			return $this;
		}
	}
