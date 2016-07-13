<?php
	namespace Edde\Ext\Resource;

	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Resource\IFileStorage;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceIndex;
	use Edde\Api\Resource\ResourceException;
	use Edde\Common\File\FileUtils;
	use Edde\Common\Resource\Resource;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Url\Url;
	use Edde\Common\Usable\UsableTrait;

	/**
	 * This is magical resource used for CSS compilation and resource checks.
	 */
	class StyleSheetResource extends Resource {
		use UsableTrait;
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
		 * list of current stylesheets
		 *
		 * @var IResource[]
		 */
		protected $styleSheetList = [];
		/**
		 * @var string
		 */
		protected $content;

		public function __construct(IFileStorage $fileStorage, IResourceIndex $resourceIndex, ITempDirectory $tempDirectory) {
			parent::__construct(new Url(), null, 'text/css');
			$this->fileStorage = $fileStorage;
			$this->resourceIndex = $resourceIndex;
			$this->tempDirectory = $tempDirectory;
		}

		public function getUrl() {
			$this->usse();
			return $this->url;
		}

		public function getName() {
			$this->usse();
			return $this->name;
		}

		public function addStryleSheet(IResource $resource) {
			$this->styleSheetList[(string)$resource->getUrl()] = $resource;
			return $this;
		}

		public function compile() {
			return $this->get();
		}

		public function get() {
			$this->usse();
			return $this->content;
		}

		protected function prepare() {
			$this->name = sha1(implode('', array_keys($this->styleSheetList))) . '.css';
			$content = [];
			foreach ($this->styleSheetList as $resource) {
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
						throw new ResourceException(sprintf('Cannot locate css [%s] resource [%s] on the filesystem.', $source, $urlList));
					}
					$resource = $this->resourceIndex->query()
						->urlLike('%' . $file)
						->resource();
					$path = $this->fileStorage->getPath($resource);
					$current = str_replace($item, '"' . $path . '"', $current);
				}
				$content[] = $current;
			}
			$this->tempDirectory->file($this->name, $this->content = implode("\n", $content));
			$this->url = FileUtils::url($this->tempDirectory->getDirectory() . '/' . $this->name);
		}
	}
