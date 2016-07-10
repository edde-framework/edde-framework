<?php
	namespace Edde\Ext\Control\Html;

	use Edde\Api\Resource\IResourceIndex;
	use Edde\Common\Control\Html\ImgControl;
	use Edde\Ext\Link\PublicLinkGenerator;

	/**
	 * This is a little bit easier to use img control connected to a PublicLinkGenerator and to a IResourceIndex.
	 */
	class ResourceImageControl extends ImgControl {
		/**
		 * @var PublicLinkGenerator
		 */
		protected $publicLinkGenerator;
		/**
		 * @var IResourceIndex
		 */
		protected $resourceIndex;

		final public function injectPublicLinkGenerator(PublicLinkGenerator $publicLinkGenerator) {
			$this->publicLinkGenerator = $publicLinkGenerator;
		}

		final public function injectResourceIndex(IResourceIndex $resourceIndex) {
			$this->resourceIndex = $resourceIndex;
		}

		public function setSrc($src) {
			parent::setSrc($this->publicLinkGenerator->generate($this->resourceIndex->getResource($src))
				->getPath());
			return $this;
		}
	}
