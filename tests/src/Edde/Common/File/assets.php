<?php
	declare(strict_types = 1);

	use Edde\Common\AbstractObject;
	use Edde\Common\File\HomeDirectoryTrait;
	use Edde\Common\Usable\UsableTrait;

	class HomeTest extends AbstractObject {
		use UsableTrait;
		use HomeDirectoryTrait;
		/**
		 * @var string
		 */
		protected $home;

		/**
		 * @param string $home
		 */
		public function __construct(string $home) {
			$this->home = $home;
		}

		public function getHome() {
			return $this->homeDirectory;
		}

		protected function prepare() {
			$this->home($this->home);
		}
	}
