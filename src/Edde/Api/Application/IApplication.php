<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	interface IApplication {
		public function run(): int;
	}
