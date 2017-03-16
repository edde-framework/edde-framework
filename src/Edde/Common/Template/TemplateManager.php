<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\File\IFile;

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

		/**
		 * @inheritdoc
		 */
		public function snippet(string $name): IFile {
			$template = $this->createTemplate();
			return $template->compile($name, $this->getResource($name));
		}
	}
