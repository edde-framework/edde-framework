<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Template\ITemplate;

	trait TemplateTrait {
		/**
		 * @var ITemplate
		 */
		protected $template;

		public function lazyTemplate(ITemplate $template) {
			$this->template = $template;
		}

		public function template(string $file) {
			$this->template->build($file, $this->getBody());
			return $this;
		}
	}
