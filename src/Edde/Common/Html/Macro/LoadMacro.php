<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\File\IRootDirectory;

	/**
	 * Load macro adds support for loading templates on demand.
	 */
	class LoadMacro extends AbstractHtmlMacro {
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;

		/**
		 * An artist, a lawyer, and a computer scientist are discussing the merits of a extra-marital affair over coffee one afternoon.
		 *
		 * The artist tells of the passion, the thrill which comes with the risk of being discovered. The lawyer warns of the difficulties. It can lead to guilt, divorce, bankruptcy. Not worth it. Too many problems.
		 *
		 * The computer scientist says "My affair is the best thing that's ever happened to me. My wife thinks I'm with my lover. My lover thinks I'm home with my wife, and I can spend all night on the computer!"
		 */
		public function __construct() {
			parent::__construct('m:load', false);
		}

		/**
		 * @param IRootDirectory $rootDirectory
		 */
		public function lazyRootDirectory(IRootDirectory $rootDirectory) {
			$this->rootDirectory = $rootDirectory;
		}

		protected function onMacro() {
			$this->write(sprintf('$this->embedd($template = self::template($this->templateManager->template(%s), $this->container));', ($helper = $this->compiler->helper($src = $this->attribute('src', false))) ? $helper : $this->load($src)), 5);
			$this->write('$template->snippet($stack->top());', 5);
		}

		/**
		 * compute path from macro's source attribute
		 *
		 * @param string $src
		 *
		 * @return string
		 */
		protected function load(string $src) {
			if (strpos($src, '/') === 0) {
				$src = $this->rootDirectory->filename(substr($src, 1));
			} else if (strpos($src, './') === 0) {
				$src = $this->compiler->getSource()
					->getDirectory()
					->filename(substr($src, 2));
			}
			return var_export($src, true);
		}
	}
