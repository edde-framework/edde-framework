<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;

	/**
	 * Macro is operating over whole Node.
	 */
	interface IMacro extends ILazyInject {
		/**
		 * @return string
		 */
		public function getName(): string;

		/**
		 * execute this macro
		 *
		 * @param INode $node
		 * @param IFile $source
		 * @param ICompiler $compiler
		 *
		 * @return
		 */
		public function macro(INode $node, IFile $source, ICompiler $compiler);
	}
