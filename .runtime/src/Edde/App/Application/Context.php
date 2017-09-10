<?php
	declare(strict_types=1);

	namespace Edde\App\Application;

	use Edde\Api\Application\IContext;
	use Edde\Common\Application\AbstractContext;

	class Context extends AbstractContext implements IContext {
		/**
		 * @inheritdoc
		 */
		public function cascade(string $delimiter, string $name = null): array {
			return ['Edde' . $delimiter . 'App' . ($name ? $delimiter . $name : '')];
		}
	}
