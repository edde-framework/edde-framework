<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Container;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependency;

	/**
	 * When there is need to search for a class in namespace hierarchy.
	 */
	abstract class AbstractDiscoveryFactory extends ClassFactory {
		public function canHandle(IContainer $container, string $dependency): bool {
			if ($discover = $this->discover($dependency)) {
				return parent::canHandle($container, $discover);
			}
			return false;
		}

		public function dependency(IContainer $container, string $dependency = null): IDependency {
			return parent::dependency($container, $this->discover($dependency));
		}

		public function execute(IContainer $container, array $parameterList, string $name = null) {
			return parent::execute($container, $parameterList, $this->discover($name));
		}

		/**
		 * @param string $name
		 *
		 * @return string|null
		 */
		abstract protected function discover(string $name);
	}
