<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Inline;

	/**
	 * Snippet is piece of template which can be called without any other dependencies.
	 */
	class SnippetInline extends AbstractHtmlInline {
		public function __construct() {
			parent::__construct('m:snippet', true);
		}

		protected function onMacro() {
			$this->macro->setMeta('snippet', true);
			$this->compiler->block($this->extract($this->macro, $this->getName(), null, false), [
				$this->macro,
			]);
		}
	}
