<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Usable\IUsable;

	interface IMacroSet extends IUsable {
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

		/**
		 * register a inline macro
		 *
		 * @param IInline $inline
		 *
		 * @return IMacroSet
		 */
		public function registerInline(IInline $inline): IMacroSet;

		/**
		 * return list of inline macros
		 *
		 * @return IInline[]
		 */
		public function getInlineList(): array;
	}
