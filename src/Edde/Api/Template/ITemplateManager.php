<?php
	declare(strict_types=1);

	namespace Edde\Api\Template;

	use Edde\Api\Config\IConfigurable;

	interface ITemplateManager extends ITemplateProvider, IConfigurable {
		/**
		 * @param ITemplateProvider $templateProvider
		 *
		 * @return ITemplateManager
		 */
		public function registerTemplateProvider(ITemplateProvider $templateProvider): ITemplateManager;

		/**
		 * build a template from the given (already registered) snippets
		 *
		 * @param string     $name
		 * @param mixed|null $context
		 */
		public function template(string $name, $context = null);
	}
