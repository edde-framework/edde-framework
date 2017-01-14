<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Cache\ICacheable;
	use Edde\Api\Runtime\IRuntime;
	use Edde\Common\Object;

	class Runtime extends Object implements IRuntime, ICacheable {
		/**
		 * @inheritdoc
		 */
		public function isConsoleMode(): bool {
			return php_sapi_name() === 'cli';
		}
	}
