<?php
	declare(strict_types = 1);

	namespace Edde\Api\Runtime;

	use Edde\Api\Usable\IDeffered;

	interface IRuntime extends IDeffered {
		public function run(callable $callback);

		/***
		 * @return bool
		 */
		public function isConsoleMode();
	}
