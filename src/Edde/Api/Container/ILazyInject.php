<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	interface ILazyInject {
		/**
		 * @param string $property
		 * @param callable $callback
		 */
		public function lazy(string $property, callable $callback);
	}
