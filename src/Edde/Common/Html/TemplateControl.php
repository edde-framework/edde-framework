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

		public function setVariable($name, $value) {
			$this->usse();
			$variableList = $this->node->getMeta('variable-list');
			$variableList[$name] = $value;
			$this->node->setMeta('variable-list', $variableList);
			return $this;
		}

		public function render() {
			$this->usse();
			$this->template($this->node->getMeta('template'), $this->node->getMeta('variable-list'));
			parent::render();
		}
	}
