<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Node\INode;

	interface IMacro {
		/**
		 * return list of supported macro names (node names)
		 *
		 * @return array
		 */
		public function getMacroList(): array;

		/**
		 * @param INode $root
		 * @param ICompiler $compiler
		 *
		 * @return IMacro
		 */
		public function run(INode $root, ICompiler $compiler);
	}
