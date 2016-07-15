<?php
	namespace Edde\Common\Web;

	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Resource\IResourceIndex;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Resource\Storage\IFileStorage;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Web\WebException;
	use Edde\Common\AbstractObject;
	use Edde\Common\File\FileUtils;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Url\Url;

	class StyleSheetCompiler extends AbstractObject implements IStyleSheetCompiler {
		/**
		 * @var IFileStorage
		 */
		protected $fileStorage;
		/**
		 * @var IResourceIndex
		 */
		protected $resourceIndex;
		/**
		 * @var ITempDirectory
		 */
		protected $tempDirectory;

		/**
		 * @param IFileStorage $fileStorage
		 * @param IResourceIndex $resourceIndex
		 * @param ITempDirectory $tempDirectory
		 */
		public function __construct(IFileStorage $fileStorage, IResourceIndex $resourceIndex, ITempDirectory $tempDirectory) {
			$this->fileStorage = $fileStorage;
			$this->resourceIndex = $resourceIndex;
			$this->tempDirectory = $tempDirectory;
		}

		public function compile(IResourceList $resourceList) {
			$content = [];
			$pathList = [];
			foreach ($resourceList->getResourceList() as $resource) {
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
					$resource = $this->resourceIndex->query()
						->urlLike('%' . $file)
						->resource();
					$this->resourceIndex->save($resource = $this->fileStorage->store($resource));
					$current = str_replace($item, '"' . ($pathList[$path] = $resource->getRelativePath()) . '"', $current);
				}
				$content[] = $current;
			}
			$this->resourceIndex->save($resource = $this->fileStorage->store($this->tempDirectory->file($resourceList->getResourceName() . '.css', implode("\n", $content))));
			return $resource;
		}
	}
