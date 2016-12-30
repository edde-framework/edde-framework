<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IFactory;
	use Edde\Common\AbstractObject;

	/**
	 * Basic implementation for all dependency factories.
	 */
	abstract class AbstractFactory extends AbstractObject implements IFactory {
		public function getFactory(IContainer $container): IFactory {
			return $this;
		}

		public function fetch(IContainer $container, string $name, array $parameterList) {
		}
	}
