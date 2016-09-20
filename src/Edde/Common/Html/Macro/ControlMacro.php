<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\Storage\IStorageDirectory;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Template\AbstractMacro;
	use Edde\Common\Usable\UsableTrait;

	/**
	 * Root control macro for template generation.
	 */
	class ControlMacro extends AbstractMacro {
		use UsableTrait;
		/**
		 * @var IStorageDirectory
		 */
		protected $storageDirectory;

		public function __construct() {
			parent::__construct('control');
		}

		public function lazyStorageDirectory(IStorageDirectory $storageDirectory) {
			$this->storageDirectory = $storageDirectory;
		}

		public function macro(INode $macro, ICompiler $compiler) {
			foreach ($macro->getNodeList() as $node) {
				$compiler->macro($node);
			}
			return true;
		}

		protected function prepare() {
		}
	}
