<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Node\INode;

	/**
	 * HtmlMacro is operating over whole Node.
	 */
	interface IMacro {
		/**
		 * compile time macros can modify node tree before compilation
		 *
		 * @return bool
		 */
		public function isCompile(): bool;

		/**
		 * runtime macro will be executed over precomputed node tree
		 *
		 * @return bool
		 */
		public function isRuntime(): bool;

		/**
		 * @return string
		 */
		public function getName(): string;

		/**
		 * @return bool
		 */
		public function hasHelperSet(): bool;

		/**
		 * @return IHelperSet
		 */
		public function getHelperSet(): IHelperSet;

		/**
		 * executed in compile time
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 */
		public function compile(INode $macro, ICompiler $compiler);

		/**
		 * executed in runtime phase
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 */
		public function macro(INode $macro, ICompiler $compiler);
	}
