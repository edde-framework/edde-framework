<?php
	namespace Edde\Common\Web;

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
		 * @param IFileStorage $fileStorage
		 * @param IResourceIndex $resourceIndex
		 */
		public function __construct(IFileStorage $fileStorage, IResourceIndex $resourceIndex) {
			$this->fileStorage = $fileStorage;
			$this->resourceIndex = $resourceIndex;
		}

		public function compile(IResourceList $resourceList) {
			$name = $resourceList->getResourceName() . '.css';
			$content = [];
			foreach ($resourceList->getResourceList() as $resource) {
				$current = $resource->get();
				$urlList = StringUtils::matchAll($current, "~url\\((?<url>.*?)\\)~", true);
				foreach (empty($urlList) ? [] : $urlList['url'] as $item) {
					$url = Url::create(str_replace([
						'"',
						"'",
					], null, $item));
					$source = $resource->getUrl()
						->getPath();
					if (($file = FileUtils::realpath(dirname($source) . '/' . $url->getPath())) === false) {
						throw new WebException(sprintf('Cannot locate css [%s] resource [%s] on the filesystem.', $source, $urlList));
					}
					$resource = $this->resourceIndex->query()
						->urlLike('%' . $file)
						->resource();
					$path = $this->fileStorage->getPath($resource);
					$current = str_replace($item, '"' . $path . '"', $current);
				}
				$content[] = $current;
			}
			return $this->fileStorage->file($name, implode("\n", $content));
		}
	}
