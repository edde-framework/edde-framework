<?php
	declare(strict_types = 1);

	namespace Foo\Bar;

	use Edde\Common\Translator\AbstractDictionary;

	class EmptyDictionary extends AbstractDictionary {
		public function translate(string $id, array $parameterList = [], string $language) {
			return null;
		}

		protected function prepare() {
		}
	}

	class DummyDictionary extends AbstractDictionary {
		public function translate(string $id, array $parameterList = [], string $language) {
			return $id . '.' . $language;
		}

		protected function prepare() {
		}
	}
