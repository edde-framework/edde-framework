<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Node\INode;

	/**
	 * HtmlMacro is operating over whole Node.
	 */
	interface IMacro {
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
		 * (optional) inline version of this macro
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 */
		public function compileInline(INode $macro, ICompiler $compiler);

		/**
		 * executed in compile time
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 */
		public function compile(INode $macro, ICompiler $compiler);

		/**
		 * (optional) inline version of this macro
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 *
		 * @return mixed
		 */
		public function macroInline(INode $macro, ICompiler $compiler);

		/**
		 * executed in runtime phase
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 */
		public function macro(INode $macro, ICompiler $compiler);
	}
