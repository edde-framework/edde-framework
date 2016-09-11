<?php
	declare(strict_types = 1);

	use Edde\Common\Converter\AbstractConverter;

	class DummyConverter extends AbstractConverter {
		public function convert($source, string $target) {
			return $source;
		}
	}

	class CleverConverter extends AbstractConverter {
		public function convert($source, string $target) {
			return $source;
		}
	}
