<?php
	namespace Edde\Ext\Resource;

	use Edde\Api\Resource\IFileStorage;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceIndex;
	use Edde\Api\Resource\ResourceException;
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
		 * list of current stylesheets
		 *
		 * @var IResource[]
		 */
		protected $styleSheetList = [];
		/**
		 * @var string
		 */
		protected $content;

		public function __construct(IFileStorage $fileStorage, IResourceIndex $resourceIndex) {
			parent::__construct(new Url(), null, 'text/css');
			$this->fileStorage = $fileStorage;
			$this->resourceIndex = $resourceIndex;
		}

		public function addStryleSheet(IResource $resource) {
			$this->styleSheetList[(string)$resource->getUrl()] = $resource;
			return $this;
		}

		public function compile() {
			return $this->get();
		}

		public function get() {
			$this->prepare();
			return $this->content;
		}

		protected function prepare() {
			$this->name = sha1(implode('', array_keys($this->styleSheetList)));
			$content = null;
			foreach ($this->styleSheetList as $resource) {
				$content = $resource->get();
				$urlList = StringUtils::matchAll($content, "~url\\((?<url>.*?)\\)~", true);
				if (empty($urlList)) {
					$this->content .= $content;
					continue;
				}
				foreach ($urlList['url'] as $url) {
					$url = Url::create(str_replace([
						'"',
						"'",
					], null, $url));
					$source = $resource->getUrl()
						->getPath();
					if (($file = str_replace('\\', '/', realpath(str_replace('\\', '/', dirname($source) . '/' . $url->getPath())))) === false) {
						throw new ResourceException(sprintf('Cannot locate css [%s] resource [%s] on the filesystem.', $source, $urlList));
					}
					$resource = $this->resourceIndex->query()
						->urlLike('%' . $file)
						->resource();
					$path = $this->fileStorage->getPath($resource);
				}
			}
			return $content;
		}
	}
