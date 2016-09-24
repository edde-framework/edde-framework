<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Inline;

	/**
	 * Snippet is piece of template which can be called without any other dependencies.
	 */
	class SnippetInline extends AbstractHtmlInline {
		/**
		 * My brother-in-law was typing on his new laptop. His ten-year-old daughter sneaked up behind him. Then she turned and ran into the kitchen, squealing to the rest of the family, "I know Daddy's password! I know Daddy's password!"
		 *
		 * "What is it? I asked her eagerly.
		 *
		 * Proudly she replied, "Asterisk, asterisk, asterisk, asterisk, asterisk!"
		 */
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
