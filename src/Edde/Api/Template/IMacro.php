<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Node\INode;

	/**
	 * Macro is operating over whole Node.
	 */
	interface IMacro {
		/**
		 * return list of supported macros
		 *
		 * @return string[]
		 */
		public function getMacroList(): array;

		/**
		 * execute this macro
		 *
		 * @param INode $node
		 * @param ICompiler $compiler
		 */
		public function macro(INode $node, ICompiler $compiler);
	}
