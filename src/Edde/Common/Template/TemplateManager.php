<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Template\ITemplate;

	class TemplateManager extends AbstractTemplateManager {
		public function template(array $nameList): ITemplate {
			$template = $this->createTemplate();
			foreach ($nameList as $name) {
				$template->import($name, $this->getResource($name));
			}
			return $template;
		}
	}
