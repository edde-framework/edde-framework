<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	class TemplateManager extends AbstractTemplateManager {
		/**
		 * @inheritdoc
		 */
		public function template(string $name, $context = null, string $namespace = null, ...$parameterList) {
			parent::template($namespace, $context ? (is_array($context) ? $context : [
				null       => $context,
				'.current' => $context,
			]) : null, $namespace, ...$parameterList);
		}
	}
