<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Crate\ICrateLoader;
	use Edde\Api\Crate\LazyCrateGeneratorTrait;
	use Edde\Api\Schema\LazySchemaManagerTrait;
	use Edde\Common\AbstractObject;
	use Edde\Common\Cache\CacheTrait;

	/**
	 * Default crate loader implementation.
	 */
	class CrateLoader extends AbstractObject implements ICrateLoader {
		use LazySchemaManagerTrait;
		use LazyCrateGeneratorTrait;
		use CacheTrait;

		/**
		 * include the requested class
		 *
		 * @param string $class
		 *
		 * @return bool
		 */
		public function __invoke(string $class) {
			$this->use();
			if (($hasSchema = $this->cache->load($cacheId = ('has-schema/' . $class))) === null) {
				$this->cache->save($cacheId, $hasSchema = $this->schemaManager->hasSchema($class));
			}
			if ($hasSchema === false) {
				return false;
			}
			$this->crateGenerator->generate();
			return class_exists($class);
		}

		protected function onBootstrap() {
			parent::onBootstrap();
			$this->cache();
		}
	}
