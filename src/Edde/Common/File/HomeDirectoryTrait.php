<?php
	declare(strict_types = 1);

	namespace Edde\Common\File;

	use Edde\Api\File\IDirectory;
	use Edde\Api\File\IRootDirectory;

	trait HomeDirectoryTrait {
		/**
		 * @var IDirectory
		 */
		protected $homeDirectory;
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;

		public function lazyRootDirectory(IRootDirectory $rootDirectory) {
			$this->rootDirectory = $rootDirectory;
		}

		public function home(string $home, IDirectory $root = null) {
			$this->lazy('homeDirectory', function () use ($home, $root) {
				$root = $root ?: $this->rootDirectory;
				$this->homeDirectory = $root->directory($home);
				$this->homeDirectory->create();
			});
		}
	}
