<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	interface IMacroSet {
		/**
		 * compile time macros (only values will be used)
		 *
		 * @return IMacro[]
		 */
		public function getCompileList(): array;

		/**
		 *
		 *
		 * @return IMacro[]
		 */
		public function getMacroList(): array;
	}
