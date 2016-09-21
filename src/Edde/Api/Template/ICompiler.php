<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;

	interface ICompiler extends ILazyInject {
		/**
		 * use the given macroset
		 *
		 * @param IMacroSet $macroSet
		 *
		 * @return ICompiler
		 */
		public function set(IMacroSet $macroSet): ICompiler;

		/**
		 * "runtime macro" - those should generate runtime
		 *
		 * @param IMacro $macro
		 *
		 * @return ICompiler
		 */
		public function registerMacro(IMacro $macro): ICompiler;

		/**
		 * @param IInline $inline
		 *
		 * @return ICompiler
		 */
		public function registerInline(IInline $inline): ICompiler;

		/**
		 * execute macro in compile time
		 *
		 * @param INode $macro
		 */
		public function execute(INode $macro);

		/**
		 * execute macro in "runtime"
		 *
		 * @param INode $macro
		 */
		public function macro(INode $macro);

		/**
		 * compile source into node; node is the final result
		 *
		 * @param IFile $source
		 *
		 * @return INode
		 */
		public function compile(IFile $source): INode;

		/**
		 * return the original source file
		 *
		 * @return IFile
		 */
		public function getSource(): IFile;

		/**
		 * if there are embedded templates, this method return current template file
		 *
		 * @return IFile
		 */
		public function getCurrent(): IFile;

		/**
		 * layout is the root (first file - getSource() === getCurrent())
		 *
		 * @return bool
		 */
		public function isLayout(): bool;

		/**
		 * build a final template; import list can contain additional set of templates (loaded before the main one)
		 *
		 * @param IFile[] $importList
		 *
		 * @return mixed
		 */
		public function template(array $importList = []);

		/**
		 * add a value to compiler context
		 *
		 * @param string $name
		 * @param mixed $value
		 *
		 * @return ICompiler
		 */
		public function setValue(string $name, $value): ICompiler;

		/**
		 * retrieve the given value from compiler's context
		 *
		 * @param string $name
		 * @param null $default
		 *
		 * @return mixed
		 */
		public function getValue(string $name, $default = null);
	}
