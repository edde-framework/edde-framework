<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Usable\IDeffered;

	interface ITemplateManager extends IDeffered {
		/**
		 * @param string $template
		 * @param array $importList compile time templates
		 */
		public function template(string $template, array $importList = []);
	}
