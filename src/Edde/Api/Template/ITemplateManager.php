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
		 * @param array $nameList
		 *
		 * @return ITemplate
		 */
		public function template(array $nameList): ITemplate;
	}
