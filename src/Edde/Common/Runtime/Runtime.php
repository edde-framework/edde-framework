<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Runtime\IRuntime;
	use Edde\Common\AbstractObject;

	class Runtime extends AbstractObject implements IRuntime {
		/**
		 * @inheritdoc
		 */
		public function isConsoleMode(): bool {
			return php_sapi_name() === 'cli';
		}
	}
