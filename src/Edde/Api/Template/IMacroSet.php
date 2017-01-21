<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	interface IMacroSet {
		/**
		 * register a macro into the set
		 *
		 * @param IMacro $macro
		 *
		 * @return IMacroSet
		 */
		public function registerMacro(IMacro $macro): IMacroSet;

		/**
		 * return set of macros
		 *
		 * @return IMacro[]
		 */
		public function getMacroList(): array;
	}