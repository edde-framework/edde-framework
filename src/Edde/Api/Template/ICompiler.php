<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;

	interface ICompiler {
		/**
		 * @param IMacro $macro
		 *
		 * @return ICompiler
		 */
		public function registerMacro(IMacro $macro): ICompiler;

		/**
		 * @param IMacro[] $macroList
		 *
		 * @return ICompiler
		 */
		public function registerMacroList(array $macroList): ICompiler;

		/**
		 * return source template file
		 *
		 * @return IFile
		 */
		public function getSource(): IFile;

		/**
		 * return destination (php) template file
		 *
		 * @return IFile
		 */
		public function getDestination(): IFile;

		/**
		 * process php value (evaluates function call/variable/...)
		 *
		 * @param string $value
		 *
		 * @return string
		 */
		public function value(string $value): string;

		/**
		 * translate file path to real file path (relative/absolute to root/absolute/...)
		 *
		 * @param string $file
		 *
		 * @return string
		 */
		public function file(string $file): string;

		/**
		 * return reference to an Edde default asset
		 *
		 * @param string $asset
		 *
		 * @return string
		 */
		public function asset(string $asset): string;

		public function compile(): ITemplate;

		public function macro(INode $root, ICompiler $compiler, callable $callback = null);
	}
