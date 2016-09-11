<?php
	declare(strict_types = 1);

	use Edde\Common\Resource\AbstractConverter;

	class DummyConverter extends AbstractConverter {
		public function convert($source, string $target) {
			return null;
		}
	}

	class CleverConverter extends AbstractConverter {
		public function convert($source, string $target) {
			return null;
		}
	}
