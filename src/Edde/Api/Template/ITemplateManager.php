<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	/**
	 * General template implementation support.
	 */
	interface ITemplateManager {
		/**
		 * @param string $template
		 * @param array  $importList compile time templates
		 *
		 * @return mixed
		 */
		public function template(string $template, array $importList = []);
	}
