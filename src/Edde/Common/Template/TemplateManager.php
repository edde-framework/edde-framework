<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	class TemplateManager extends AbstractTemplateManager {
		/**
		 * @inheritdoc
		 */
		public function template(string $name, $context = null, string $namespace = null, ...$parameterList) {
			if ($context) {
				$context = is_array($context) ? $context : [null => $context];
				$context['.current'] = $context[null];
			}
			/** @noinspection PhpIncludeInspection */
			require $this->snippet($name, $namespace, ...$parameterList);
		}
	}
