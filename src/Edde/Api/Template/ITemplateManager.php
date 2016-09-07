<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\File\IFile;
	use Edde\Api\Usable\IUsable;

	interface ITemplateManager extends IUsable {
		/**
		 * @param IMacro[] $macroList
		 *
		 * @return ITemplateManager
		 */
		public function registerMacroList(array $macroList): ITemplateManager;

		/**
		 * compiles the given template file; if force is true, template is regenerated
		 *
		 * @param IFile $file
		 * @param bool $force
		 *
		 * @return IFile
		 */
		public function compile(IFile $file, bool $force = false): IFile;

		/**
		 * shorthand for compile
		 *
		 * @param string $file
		 * @param array $parameterList
		 * @param bool $force
		 *
		 * @return ITemplate
		 */
		public function template(string $file, array $parameterList = [], bool $force = false): ITemplate;
	}
