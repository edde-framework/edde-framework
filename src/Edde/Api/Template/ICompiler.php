<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;

	interface ICompiler extends ILazyInject {
		/**
		 * add a compile time macro
		 *
		 * @param IMacro $macro
		 *
		 * @return ICompiler
		 */
		public function registerCompileMacro(IMacro $macro): ICompiler;

		/**
		 * "runtime macro" - those should generate runtime
		 *
		 * @param IMacro $macro
		 *
		 * @return ICompiler
		 */
		public function registerMacro(IMacro $macro): ICompiler;

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
		 * execute whole compilation process: compile + template building (generating)
		 *
		 * @param INode $template source node (prepared from compile)
		 *
		 * @return mixed
		 */
		public function template(INode $template = null);

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
