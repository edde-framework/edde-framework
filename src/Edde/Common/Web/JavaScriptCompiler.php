<?php
	declare(strict_types = 1);

	namespace Edde\Common\Web;

	use Edde\Api\File\IFile;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Resource\Storage\IFileStorage;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Usable\UsableTrait;

	class JavaScriptCompiler extends AbstractCompiler implements IJavaScriptCompiler {
		use UsableTrait;
		use CacheTrait;
		/**
		 * @var IFileStorage
		 */
		protected $fileStorage;
		/**
		 * @var ITempDirectory
		 */
		protected $tempDirectory;

		public function lazyFileStorage(IFileStorage $fileStorage) {
			$this->fileStorage = $fileStorage;
		}

		public function lazyTempDirectory(ITempDirectory $tempDirectory) {
			$this->tempDirectory = $tempDirectory;
		}

		public function getPathList(): array {
			$pathList = [];
			foreach ($this->resourceList as $resource) {
				$resource = $this->fileStorage->store($resource);
				$pathList[$url] = $url = (string)$resource->getRelativePath();
			}
			return $pathList;
		}

		public function compile(IResourceList $resourceList): IFile {
			$this->use();
			$content = [];
			if (($file = $this->cache->load($cacheId = $resourceList->getResourceName())) === null) {
				foreach ($resourceList as $resource) {
					$content[] = $resource->get();
				}
				$this->cache->save($cacheId, $file = $this->fileStorage->store($this->tempDirectory->save($resourceList->getResourceName() . '.js', implode(";\n", $content))));
			}
			return $file;
		}

		protected function prepare() {
			$this->cache();
		}
	}
