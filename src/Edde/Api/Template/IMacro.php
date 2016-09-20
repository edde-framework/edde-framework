<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Node\INode;

	/**
	 * HtmlMacro is operating over whole Node.
	 */
	interface IMacro extends ILazyInject {
		/**
		 * @return string
		 */
		public function getName(): string;

		/**
		 * execute this macro
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 */
		public function macro(INode $macro, ICompiler $compiler);
	}
