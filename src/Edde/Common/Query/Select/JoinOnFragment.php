<?php
	namespace Edde\Common\Query\Select;

	use Edde\Common\Query\AbstractFragment;

	class JoinOnFragment extends AbstractFragment {
		public function on() {
			return new JoinExpressionFragment($this->node);
		}
	}
