<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Container\IConfigurable;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependency;

	/**
	 * Interface to class binding factory.
	 */
	class InterfaceFactory extends ClassFactory {
		/**
		 * @var string
		 */
		protected $interface;
		/**
		 * @var string
		 */
		protected $class;
		/**
		 * @var mixed
		 */
		protected $instance;

		/**
		 * Practical thought:
		 * A husband is supposed to make his wife's panties wet, not her eyes.
		 * A wife is supposed to make her husband's dick hard, not his life...!
		 *
		 * @param string $interface
		 * @param string $class
		 */
		public function __construct(string $interface, string $class) {
			$this->interface = $interface;
			$this->class = $class;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(IContainer $container, string $dependency): bool {
			return $dependency === $this->interface;
		}

		/**
		 * @inheritdoc
		 */
		public function dependency(IContainer $container, string $dependency = null): IDependency {
			return parent::dependency($container, $this->class);
		}

		/**
		 * @inheritdoc
		 */
		public function fetch(IContainer $container, string $id, ICache $cache) {
			if ($this->instance) {
				return $this->instance;
			}
			return $this->instance = $cache->load($id);
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IContainer $container, array $parameterList, string $name = null) {
			return $this->instance ?: $this->instance = parent::execute($container, $parameterList, $this->class);
		}

		/**
		 * @inheritdoc
		 */
		public function push(IContainer $container, string $id, $instance, ICache $cache) {
			if ($instance instanceof IConfigurable) {
				$instance->config();
			}
			$cache->save($id, $instance);
		}
	}
