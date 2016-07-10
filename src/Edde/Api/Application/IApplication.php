<?php
	namespace Edde\Api\Application;

	use Edde\Api\Usable\IUsable;

	interface IApplication extends IUsable {
		public function run();
	}
