<?php
	declare(strict_types=1);

	namespace Edde\Common\Resource;

	use Edde\Api\Resource\IResourceProvider;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractResourceProvider extends Object implements IResourceProvider {
		use ConfigurableTrait;
		use CacheTrait;

		/**
		 * @inheritdoc
		 */
		public function hasResource(string $name, ...$parameters): bool {
			$cache = $this->cache();
			if (($hasResource = $cache->load($cacheId = ('resource-' . $name))) !== null) {
				return $hasResource;
			}
			try {
				$this->getResource($name, ...$parameters);
				return $hasResource = true;
			} catch (UnknownResourceException $exception) {
				return $hasResource = false;
			} finally {
				$cache->save($cacheId, $hasResource);
			}
		}
	}
