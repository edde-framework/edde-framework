<?php
	declare(strict_types=1);

	namespace Edde\Api\Template;

	use Edde\Api\Resource\IResource;

	interface ITemplate {
		/**
		 * from where this template can got another templates?
		 *
		 * @param ITemplateProvider $templateProvider
		 *
		 * @return ITemplate
		 */
		public function registerTemplateProvider(ITemplateProvider $templateProvider): ITemplate;

		/**
		 * add list of resources of this template
		 *
		 * @param IResource $resource
		 *
		 * @return ITemplate
		 */
		public function import(string $name, IResource $resource): ITemplate;

		/**
		 * execute template compilation
		 */
		public function compile();
	}
