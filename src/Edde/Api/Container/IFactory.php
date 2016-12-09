<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	use Edde\Api\Cache\ICache;

	/**
	 * Factory is general way how to build a dependency with the final set of parameters/dependencies.
	 */
	interface IFactory {
		/**
		 * @param ICache $cache
		 *
		 * @return IFactory
		 */
		public function setCache(ICache $cache): IFactory;

		/**
		 * is this factory able to handle the given input?
		 *
		 * @param mixed $dependency
		 *
		 * @return bool
		 */
		public function canHandle($dependency): bool;

		/**
		 * @param $dependency
		 *
		 * @return IDependency
		 */
		public function dependency($dependency): IDependency;

		/**
		 * 90% usecase is to return self, but in some rare cases factory can return another factory
		 *
		 * @return IFactory
		 */
		public function getFactory(): IFactory;
	}
