<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Usable\AbstractUsable;

	class DummyCrateGenerator extends AbstractUsable implements ICrateGenerator {
		public function compile(ISchema $schema): array {
			return [];
		}

		public function generate(bool $force = false): ICrateGenerator {
			return $this;
		}

		public function include (): ICrateGenerator {
			return $this;
		}

		protected function prepare() {
		}
	}
