<?php
	declare(strict_types = 1);

	namespace Edde\Api\Application;

	use Edde\Api\Usable\IUsable;

	interface IApplication extends IUsable {
		public function run();
	}
