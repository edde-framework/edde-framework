<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	/**
	 * Formal template factory; it has to be used from the container
	 */
	interface ITemplateFactory {
		/**
		 * @return ITemplate
		 */
		public function create(): ITemplate;
	}
