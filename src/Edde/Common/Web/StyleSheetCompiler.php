<?php
	declare(strict_types = 1);

	namespace Edde\Common\Web;

	use Edde\Api\File\IFile;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Web\WebException;
	use Edde\Common\File\File;
	use Edde\Common\File\FileUtils;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Url\Url;
	use Edde\Common\Usable\UsableTrait;

	class StyleSheetCompiler extends AbstractCompiler implements IStyleSheetCompiler {
		use UsableTrait;
		/**
		 * ignored url schemes
		 *
		 * @var array
		 */
		static private $schemeList = [
			'data',
		];
		/**
		 * @var ITempDirectory
		 */
		protected $tempDirectory;

		/**
		 * @param ITempDirectory $tempDirectory
		 */
		public function lazyTempDirectory(ITempDirectory $tempDirectory) {
			$this->tempDirectory = $tempDirectory;
		}

		public function compile(IResourceList $resourceList): IFile {
			$this->use();
			$content = [];
			$pathList = [];
			if (($file = $this->cache->load($cacheId = $resourceList->getResourceName())) === null) {
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
						if (in_array($url->getScheme(), self::$schemeList, true)) {
							continue;
						}
						if (isset($pathList[$path = $url->getPath()])) {
							$current = str_replace($item, '"' . $pathList[$path] . '"', $current);
							continue;
						}
						if (($file = FileUtils::realpath($resourcePath . '/' . $path)) === false) {
							throw new WebException(sprintf('Cannot locate css [%s] resource [%s] on the filesystem.', $source, $urlList));
						}
						$current = str_replace($item, '"' . ($pathList[$path] = $this->assetStorage->store(new File($file))
								->getRelativePath()) . '"', $current);
					}
					$content[] = $current;
				}
				$this->cache->save($cacheId, $file = $this->assetStorage->store($this->tempDirectory->save($resourceList->getResourceName() . '.css', implode("\n", $content))));
			}
			return $file;
		}
	}
