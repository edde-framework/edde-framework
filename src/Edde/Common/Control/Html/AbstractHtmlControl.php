<?php
	namespace Edde\Common\Control\Html;

	use Edde\Api\Control\Html\IHtmlControl;
	use Edde\Common\Control\AbstractControl;

	abstract class AbstractHtmlControl extends AbstractControl implements IHtmlControl {
		public function setTag($tag, $pair = true) {
			$this->usse();
			$this->node->setAttributeList([
				'tag' => $tag,
				'pair' => $pair,
			]);
			return $this;
		}

		public function setId($id) {
			$this->usse();
			$this->node->setAttribute('id', $id);
			return $this;
		}

		public function getId() {
			$this->usse();
			return $this->node->getAttribute('id');
		}

		public function setAttribute($attribute, $value) {
			$this->usse();
			$attributeList = $this->getAttributeList();
			$attributeList[$attribute] = $value;
			$this->node->setAttribute('attribute-list', $attributeList);
			return $this;
		}

		public function getAttributeList() {
			$this->usse();
			return $this->node->getAttribute('attribute-list', []);
		}

		public function hasAttribute($attribute) {
			$attributeList = $this->getAttributeList();
			return empty($attributeList[$attribute]) === false;
		}

		public function addClass($class) {
			$this->addAttribute('class', $class);
			return $this;
		}

		public function addAttribute($attribute, $value) {
			$this->usse();
			$attributeList = $this->getAttributeList();
			$attributeList[$attribute][] = $value;
			$this->node->setAttribute('attribute-list', $attributeList);
			return $this;
		}

		public function hasClass($class) {
			$classList = $this->getClassList();
			return isset($classList[$class]);
		}

		public function getClassList() {
			$attributeList = $this->getAttributeList();
			return isset($attributeList['class']) ? $attributeList['class'] : [];
		}

		public function render() {
			$this->usse();
			/** @var $control IHtmlControl */
			if (($tag = $this->getTag()) === null) {
				foreach ($this->getControlList() as $control) {
					$control->render();
				}
				return $this;
			}
			$indent = str_repeat("\t", $this->node->getLevel());
			echo $indent;
			echo '<' . $this->getTag();
			foreach ($this->getAttributeList() as $name => $list) {
				if (is_array($list)) {
					echo ' ' . $name . '="' . implode(' ', $list) . '"';
					continue;
				}
				echo ' ' . $name . '="' . $list . '"';
			}
			$pair = '>';
			echo $pair;
			$newline = "\n";
			if (($value = $this->node->getValue()) !== null) {
				$newline = null;
				$indent = null;
				echo $value;
			}
			if ($this->node->isLeaf() && $this->isPair() === true) {
				$newline = null;
				$indent = null;
			}
			echo $newline;
			foreach ($this->getControlList() as $control) {
				$control->render();
			}
			if ($this->isPair()) {
				echo $indent . '</' . $this->getTag() . ">\n";
			}
			return $this;
		}

		public function getTag() {
			$this->usse();
			return $this->node->getAttribute('tag');
		}

		public function isPair() {
			$this->usse();
			return $this->node->getAttribute('pair', true);
		}
	}
