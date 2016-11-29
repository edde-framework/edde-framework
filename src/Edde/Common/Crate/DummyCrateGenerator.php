<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\AbstractObject;

	class DummyCrateGenerator extends AbstractObject implements ICrateGenerator {
		public function compile(ISchema $schema): array {
			return [];
		}

		public function generate(bool $force = false): ICrateGenerator {
			return $this;
		}

		public function include (): ICrateGenerator {
			return $this;
		}
	}
