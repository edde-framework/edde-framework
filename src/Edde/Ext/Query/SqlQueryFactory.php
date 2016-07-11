<?php
	namespace Edde\Ext\Query;

	use Edde\Common\Query\AbstractStaticQueryFactory;

	class SqlQueryFactory extends AbstractStaticQueryFactory {
		protected function delimite($delimite) {
			return '"' . str_replace('"', '""', $delimite) . '"';
		}

		protected function quote($quote) {
			return "[$quote]";
		}

		protected function type($type) {
			return $type;
		}
	}
