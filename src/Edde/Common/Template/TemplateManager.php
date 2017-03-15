<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\File\IFile;
	use Edde\Api\Template\ITemplate;

	class TemplateManager extends AbstractTemplateManager {
		/**
		 * @inheritdoc
		 */
		public function template(string $name, $context = null): ITemplate {
			$template = $this->snippet($name, $context);
			return $template;
		}

		/**
		 * @inheritdoc
		 */
		public function snippet(string $name, $context = null): IFile {
			$template = $this->createTemplate();
			$template->compile($name, $this->getResource($name));
			// return
		}
	}
