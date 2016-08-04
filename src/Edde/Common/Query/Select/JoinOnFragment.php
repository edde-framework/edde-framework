<?php
	declare(strict_types = 1);

	namespace Edde\Common\Query\Select;

	use Edde\Common\Query\AbstractFragment;

	class JoinOnFragment extends AbstractFragment {
		public function on() {
			return new JoinExpressionFragment($this->node);
		}
	}
