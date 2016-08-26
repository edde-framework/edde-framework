<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Control\IControl;

	class TemplateControl extends AbstractHtmlControl {
		use TemplateTrait;

		public function setTemplate(string $template) {
			$this->use();
			$this->node->setMeta('template', $template);
			return $this;
		}

		public function render() {
			$this->use();
			return parent::render();
		}

		public function dirty(bool $dirty = true): IControl {
			$this->use();
			if ($dirty) {
				$this->template($this->node->getMeta('template'));
			}
			return parent::dirty($dirty);
		}
	}
