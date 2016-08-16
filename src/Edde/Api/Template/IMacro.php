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
		 * if the macro knows the given input value, it should convert it to the proper php snippet
		 *
		 * @param string $string
		 *
		 * @return string|null
		 */
		public function variable(string $string);

		/**
		 * @param INode $root
		 * @param ICompiler $compiler
		 *
		 * @return IMacro
		 */
		public function run(INode $root, ICompiler $compiler);
	}
