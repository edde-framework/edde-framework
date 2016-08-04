<?php
	declare(strict_types = 1);

	namespace Edde\Api\Runtime;

	use Edde\Api\Usable\IUsable;

	interface IRuntime extends IUsable {
		public function run(callable $callback);

		/***
		 * @return bool
		 */
		public function isConsoleMode();
	}
