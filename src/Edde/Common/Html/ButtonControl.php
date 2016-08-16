<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	class ButtonControl extends AbstractHtmlControl {
		public function setTitle($title) {
			$this->use();
			$this->setAttribute('title', $title);
			return $this;
		}

		public function setAttribute($attribute, $value) {
			$this->use();
			switch ($attribute) {
				case 'value':
					$this->node->setValue($value);
					break;
				case 'title':
					$this->node->setAttribute('title', $value);
					break;
				case 'bind':
					$this->node->setAttribute('data-bind', $value);
					break;
				default:
					parent::setAttribute($attribute, $value);
			}
			return $this;
		}

		public function setHint($hint) {
			$this->setAttribute('hint', $hint);
			return $this;
		}

		public function setAction($control, $action) {
			$this->addAttributeList([
				'data-control' => $control,
				'data-action' => $action,
			]);
			return $this;
		}

		protected function prepare() {
			parent::prepare();
			$this->setTag('div', true);
			$this->addClass('button edde-clickable');
		}
	}
