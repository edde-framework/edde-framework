<?php
	namespace Edde\Common\Resource;

	use Edde\Api\File\FileException;
	use Edde\Api\Url\IUrl;
	use Edde\Common\File\FileUtils;

	class FileResource extends Resource {
		/**
		 * @param string|IUrl $file
		 * @param string|null $base
		 *
		 * @throws FileException
		 */
		public function __construct($file, $base = null) {
			parent::__construct($file instanceof IUrl ? $file : FileUtils::url($file), $base);
		}

		public function getName() {
			if ($this->name === null) {
				$this->name = $this->url->getResourceName();
			}
			return $this->name;
		}

		public function getMime() {
			if ($this->mime === null) {
				$this->mime = FileUtils::mime($this->url->getAbsoluteUrl());
			}
			return $this->mime;
		}
	}
