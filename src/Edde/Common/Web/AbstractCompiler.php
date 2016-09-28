<?php
	declare(strict_types = 1);

	namespace Edde\Common\Web;

	use Edde\Api\Asset\LazyAssetStorageTrait;
	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Web\ICompiler;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Deffered\DefferedTrait;
	use Edde\Common\Resource\ResourceList;

	abstract class AbstractCompiler extends ResourceList implements ICompiler, ILazyInject {
		use LazyAssetStorageTrait;
		use CacheTrait;
		use DefferedTrait;

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
	}
