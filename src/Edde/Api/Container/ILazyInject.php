<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	interface ILazyInject {
		/**
		 * register the given container dependency on the given property
		 *
		 * @param string     $property
		 * @param IContainer $container
		 * @param string     $dependency
		 * @param array      $parameterList
		 *
		 * @return $this
		 */
		public function lazy(string $property, IContainer $container, string $dependency, array $parameterList = []);
	}
