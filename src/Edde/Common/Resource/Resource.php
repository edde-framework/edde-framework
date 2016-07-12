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
		 * friendly name of this resource
		 *
		 * @var string
		 */
		protected $name;
		/**
		 * @var string
		 */
		protected $mime;

		/**
		 * @param IUrl $url
		 * @param string|null $name
		 * @param string|null $mime
		 */
		public function __construct(IUrl $url, $name = null, $mime = null) {
			$this->url = $url;
			$this->name = $name;
			$this->mime = $mime;
		}

		public function getUrl() {
			return $this->url;
		}

		public function getName() {
			return $this->name;
		}

		public function getMime() {
			return $this->mime;
		}

		public function get() {
			return file_get_contents($this->url->getAbsoluteUrl());
		}
	}
