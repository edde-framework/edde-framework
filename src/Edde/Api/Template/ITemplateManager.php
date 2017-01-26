<?php
	declare(strict_types=1);

	namespace Edde\Api\Template;

	use Edde\Api\Config\IConfigurable;

	/**
	 * General template implementation support.
	 */
	interface ITemplateManager extends IConfigurable {
		/**
		 * @param string $template
		 * @param array  $importList compile time templates
		 *
		 * @return mixed
		 */
		public function template(string $template, array $importList = []);
	}
