<?php
	declare(strict_types = 1);

	namespace Edde\Common\Web;

	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Resource\Storage\IFileStorage;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Web\WebException;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\File\File;
	use Edde\Common\File\FileUtils;
	use Edde\Common\Resource\ResourceList;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Url\Url;
	use Edde\Common\Usable\UsableTrait;

	class StyleSheetCompiler extends ResourceList implements IStyleSheetCompiler {
		use LazyInjectTrait;
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

		public function compile(IResourceList $resourceList): IResource {
			$this->usse();
			$content = [];
			$pathList = [];
			if (($resource = $this->cache->load($cacheId = $resourceList->getResourceName())) === null) {
				foreach ($resourceList as $resource) {
					if ($resource->isAvailable() === false) {
						throw new WebException(sprintf('Cannot compile stylesheets: resource [%s] is not available (does not exists?).', (string)$resource->getUrl()));
					}
					$current = $resource->get();
					$urlList = StringUtils::matchAll($current, "~url\\((?<url>.*?)\\)~", true);
					$resourcePath = $source = $resource->getUrl()
						->getPath();
					$resourcePath = dirname($resourcePath);
					foreach (empty($urlList) ? [] : array_unique($urlList['url']) as $item) {
						$url = Url::create(str_replace([
							'"',
							"'",
						], null, $item));
						if (isset($pathList[$path = $url->getPath()])) {
							$current = str_replace($item, '"' . $pathList[$path] . '"', $current);
							continue;
						}
						if (($file = FileUtils::realpath($resourcePath . '/' . $path)) === false) {
							throw new WebException(sprintf('Cannot locate css [%s] resource [%s] on the filesystem.', $source, $urlList));
						}
						$resource = $this->fileStorage->store(new File($file));
						$current = str_replace($item, '"' . ($pathList[$path] = $resource->getRelativePath()) . '"', $current);
					}
					$content[] = $current;
				}
				$this->cache->save($cacheId, $resource = $this->fileStorage->store($this->tempDirectory->save($resourceList->getResourceName() . '.css', implode("\n", $content))));
			}
			return $resource;
		}

		protected function prepare() {
			$this->cache();
		}
	}
