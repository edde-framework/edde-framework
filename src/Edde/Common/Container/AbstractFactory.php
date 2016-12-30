<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Common\AbstractObject;

	/**
	 * Basic implementation for all dependency factories.
	 */
	abstract class AbstractFactory extends AbstractObject implements IFactory {
		/**
		 * @inheritdoc
		 */
		public function getFactory(IContainer $container): IFactory {
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function fetch(IContainer $container, string $id, ICache $cache) {
		}

		/**
		 * @inheritdoc
		 */
		public function push(IContainer $container, string $id, $instance, ICache $cache) {
		}
	}
