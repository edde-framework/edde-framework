<?php
	declare(strict_types = 1);

	namespace Edde\Common\File;

	use Edde\Api\File\FileException;
	use Edde\Api\File\IFile;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Resource\Resource;

	class File extends Resource implements IFile {
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
	}
