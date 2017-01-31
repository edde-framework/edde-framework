<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Template\TemplateException;

	class Template extends AbstractTemplate {
		/**
		 * @inheritdoc
		 */
		public function compile() {
			if (empty($this->resourceList)) {
				throw new TemplateException(sprintf('Resource list is empty;cannot build a template.'));
			}
		}
	}
