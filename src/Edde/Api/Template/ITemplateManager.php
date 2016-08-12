<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Resource\IResource;
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
		 * @param IResource $resource
		 *
		 * @return ITemplate
		 */
		public function compile(IResource $resource): ITemplate;

		/**
		 * shorthand for compile
		 *
		 * @param string $file
		 *
		 * @return ITemplate
		 */
		public function template(string $file): ITemplate;
	}
