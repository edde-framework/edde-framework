<?php
	declare(strict_types = 1);

	namespace Edde\Common\Web;

	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Resource\Storage\IFileStorage;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Api\Web\WebException;
	use Edde\Common\AbstractObject;
	use Edde\Common\File\FileUtils;
	use Edde\Common\Resource\FileResource;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Url\Url;

	class StyleSheetCompiler extends AbstractObject implements IStyleSheetCompiler {
		/**
		 * @var IFileStorage
		 */
		protected $fileStorage;
		/**
		 * @var ITempDirectory
		 */
		protected $tempDirectory;

		/**
		 * @param IFileStorage $fileStorage
		 * @param ITempDirectory $tempDirectory
		 */
		public function __construct(IFileStorage $fileStorage, ITempDirectory $tempDirectory) {
			$this->fileStorage = $fileStorage;
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
					$resource = $this->fileStorage->store(new FileResource($file));
					$current = str_replace($item, '"' . ($pathList[$path] = $resource->getRelativePath()) . '"', $current);
				}
				$content[] = $current;
			}
			return $this->fileStorage->store($this->tempDirectory->save($resourceList->getResourceName() . '.css', implode("\n", $content)));
		}
	}
