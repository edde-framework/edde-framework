<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Crate\ICrateLoader;
	use Edde\Api\Crate\LazyCrateGeneratorTrait;
	use Edde\Api\Schema\LazySchemaManagerTrait;
	use Edde\Common\AbstractObject;

	/**
	 * Default crate loader implementation.
	 */
	class CrateLoader extends AbstractObject implements ICrateLoader, ILazyInject {
		use LazySchemaManagerTrait;
		use LazyCrateGeneratorTrait;

		/**
		 * include the requested class
		 *
		 * @param string $class
		 *
		 * @return bool
		 */
		public function __invoke(string $class) {
			if ($this->schemaManager->hasSchema($class) === false) {
				return false;
			}
			$this->crateGenerator->generate();
			return class_exists($class);
		}
	}
