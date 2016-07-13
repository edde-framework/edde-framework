<?php
	namespace Edde\Common\Resource;

	use Edde\Api\Url\IUrl;
	use Edde\Common\File\FileUtils;

	class FileResource extends Resource {
		/**
		 * @param string $file
		 */
		public function __construct($file) {
			parent::__construct($file instanceof IUrl ? $file : FileUtils::url($file), null, null);
		}

		public function getMime() {
			if ($this->mime === null) {
				$this->mime = FileUtils::mime($this->url->getAbsoluteUrl());
			}
			return $this->mime;
		}
	}
