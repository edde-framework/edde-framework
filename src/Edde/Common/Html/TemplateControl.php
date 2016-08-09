<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	class TemplateControl extends AbstractHtmlControl {
		use TemplateTrait;

		public function setTemplate($template) {
			$this->usse();
			$this->node->setMeta('template', $template);
			return $this;
		}

		public function render() {
			$this->usse();
			$this->template($this->node->getMeta('template'));
			parent::render();
		}
	}
