<?php
	declare(strict_types=1);

	namespace Edde\Api\Template;

	trait LazyTemplateProviderTrait {
		/**
		 * @var ITemplateProvider
		 */
		protected $templateProvider;

		/**
		 * @param ITemplateProvider $templateProvider
		 */
		public function lazyTemplateProvider(ITemplateProvider $templateProvider) {
			$this->templateProvider = $templateProvider;
		}
	}
