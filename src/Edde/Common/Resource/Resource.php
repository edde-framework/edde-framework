<?php
	namespace Edde\Common\Resource;

	use Edde\Api\Resource\IResource;
	use Edde\Api\Url\IUrl;
	use Edde\Common\AbstractObject;

	class Resource extends AbstractObject implements IResource {
		/**
		 * @var IUrl
		 */
		protected $url;

		/**
		 * @param IUrl $url
		 */
		public function __construct(IUrl $url) {
			$this->url = $url;
		}

		public function getUrl() {
			return $this->url;
		}
	}
