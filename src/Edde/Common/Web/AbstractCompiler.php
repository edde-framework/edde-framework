<?php
	declare(strict_types = 1);

	namespace Edde\Common\Web;

	use Edde\Api\Asset\IAssetStorage;
	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Web\ICompiler;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Resource\ResourceList;

	abstract class AbstractCompiler extends ResourceList implements ICompiler, ILazyInject {
		use CacheTrait;
		/**
		 * @var IAssetStorage
		 */
		protected $assetStorage;

		/**
		 * @param IAssetStorage $assetStorage
		 */
		public function lazyAssetStorage(IAssetStorage $assetStorage) {
			$this->assetStorage = $assetStorage;
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function getPathList(): array {
			$pathList = [];
			foreach ($this->resourceList as $resource) {
				$resource = $this->assetStorage->store($resource);
				$pathList[$url] = $url = (string)$resource->getRelativePath();
			}
			return $pathList;
		}

		protected function prepare() {
			$this->cache();
		}
	}
