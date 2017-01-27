<?php
	declare(strict_types = 1);

	namespace Edde\Api\TemplateEngine;

	/**
	 * Lazy dependency for the global set of macros.
	 */
	trait LazyMacroSetTrait {
		/**
		 * @var IMacroSet
		 */
		protected $macroSet;

		/**
		 * @param IMacroSet $macroSet
		 */
		public function lazyMacroSet(IMacroSet $macroSet) {
			$this->macroSet = $macroSet;
		}
	}
