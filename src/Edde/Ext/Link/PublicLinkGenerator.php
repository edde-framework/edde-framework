<?php
	namespace Edde\Ext\Link;

	use Edde\Api\Link\ILinkGenerator;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\ResourceException;
	use Edde\Common\Url\Url;
	use Edde\Common\Usable\AbstractUsable;

	class PublicLinkGenerator extends AbstractUsable implements ILinkGenerator {
		/**
		 * root folder for a relative path generation
		 *
		 * @var string
		 */
		protected $root;
		/**
		 * public folder; root should be subset of this folder
		 *
		 * @var string
		 */
		protected $public;

		/**
		 * @param string $root
		 * @param string $public
		 */
		public function __construct($root, $public) {
			$this->root = $root;
			$this->public = $public;
		}

		public function generate($generate) {
			$this->usse();
			if (($generate instanceof IResource) === false) {
				throw new ResourceException(sprintf('Unsuported type for [%s]; parameter should be instance of [%s].', gettype($generate), IResource::class));
			}
			/** @var $resource IResource */
			$resource = $generate;
			$url = $resource->getUrl();
			$path = $this->public . '/' . sha1(dirname($url->getPath()));
			$file = $path . '/' . $url->getResourceName();
			if (@mkdir($path, 0777, true) && is_dir($path) === false) {
				throw new ResourceException(sprintf('Cannot create public folder [%s] for the resource [%s].', $path, $url));
			}
			copy($url, $file);
			return Url::create(str_replace([
				$this->root,
				'\\',
			], [
				null,
				'/',
			], $file));
		}

		protected function prepare() {
			if (strpos($this->public, $this->root) === false) {
				throw new ResourceException(sprintf('Root folder [%s] should be subpath of the public folder [%s].', $this->root, $this->public));
			}
		}
	}
