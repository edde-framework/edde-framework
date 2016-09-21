<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Usable\IUsable;

	interface IMacroSet extends IUsable {
		/**
		 * return set of macros
		 *
		 * @return IMacro[]
		 */
		public function getMacroList(): array;

		/**
		 * return list of inline macros
		 *
		 * @return IInline[]
		 */
		public function getInlineList(): array;
	}
