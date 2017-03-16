<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	class TemplateManager extends AbstractTemplateManager {
		/**
		 * @inheritdoc
		 */
		public function template(string $name, $context = null) {
			$context = $context ? (is_array($context) ? $context : [null => $context]) : null;
			/** @noinspection PhpIncludeInspection */
			require $this->snippet($name);
			$this->template = null;
		}
	}
