<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Usable\IUsable;

	interface ITemplateManager extends IUsable {
		/**
		 * @param IMacro $macro
		 *
		 * @return ITemplateManager
		 */
		public function registerMacro(IMacro $macro): ITemplateManager;

		/**
		 * @param IMacro[] $macroList
		 *
		 * @return ITemplateManager
		 */
		public function registerMacroList(array $macroList): ITemplateManager;

		/**
		 * @param INode $root
		 * @param ITemplate $template
		 * @param IFile $file
		 * @param array $parameterList
		 *
		 * @return mixed
		 * @internal param IResource $resource
		 */
		public function macro(INode $root, ITemplate $template, IFile $file, ...$parameterList);

		/**
		 * @param IFile $file
		 *
		 * @return ITemplate
		 * @internal param IResource $resource
		 *
		 */
		public function compile(IFile $file): ITemplate;

		/**
		 * shorthand for compile
		 *
		 * @param string $file
		 *
		 * @return ITemplate
		 */
		public function template(string $file): ITemplate;
	}
