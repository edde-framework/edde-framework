<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Container\IFactory;
	use Edde\Common\AbstractObject;

	/**
	 * Basic implementation for all dependency factories.
	 */
	abstract class AbstractFactory extends AbstractObject implements IFactory {
		/**
		 * @var ICache
		 */
		protected $cache;

		/**
		 * @inheritdoc
		 */
		public function setCache(ICache $cache): IFactory {
			$this->cache = $cache;
			return $this;
		}

		protected function save(string $id, $value) {
			$this->cache ? $this->cache->save($id, $value) : null;
			return $this;
		}

		protected function load(string $id, $default = null) {
			return $this->cache ? $this->cache->load($id, $default) : $default;
		}
	}
