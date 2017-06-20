<?php
	declare(strict_types=1);

	namespace Edde\App\Application;

	use Edde\Common\Application\AbstractContext;

	/**
	 * Demo application's context
	 */
	class AppContext extends AbstractContext {
		/**
		 * @inheritdoc
		 */
		public function cascade(string $delimiter, string $name = null): array {
			return ['Edde' . $delimiter . 'App' . ($name ? $delimiter . $name : '')];
		}
	}
