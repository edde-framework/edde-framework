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
		 * @param INode $root
		 *
		 * @return
		 */
		public function compileInline(INode $macro, ICompiler $compiler, INode $root);

		/**
		 * executed in compile time
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 * @param INode $root
		 */
		public function compile(INode $macro, ICompiler $compiler, INode $root);

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
