<?php
	declare(strict_types = 1);

	namespace Edde\Api\Crate;

	use Edde\Api\Schema\ISchema;

	interface ICrateGenerator {
		/**
		 * generate source class (php source code) for the given schema
		 *
		 * @param ISchema $schema
		 *
		 * @return string[] array of crates with dependencies (key is crate FQN)
		 */
		public function compile(ISchema $schema);

		/**
		 * @return $this
		 */
		public function generate();
	}
