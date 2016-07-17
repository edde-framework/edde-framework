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
			$this->setAttribute('id', $id);
			return $this;
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

		public function getId() {
			$this->usse();
			return $this->getAttribute('id');
		}

		public function getAttribute($name, $default = null) {
			$attributeList = $this->node->getAttribute('attribute-list', []);
			return isset($attributeList[$name]) || array_key_exists($name, $attributeList) ? $attributeList[$name] : $default;
		}

		public function setText($text) {
			$this->usse();
			$this->node->setValue($text);
			return $this;
		}

		public function setAttributeList(array $attributeList) {
			$this->usse();
			$this->node->setAttribute('attribute-list', $attributeList);
			return $this;
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

		public function send() {
			echo $this->render();
		}

		public function render() {
			$this->usse();
			$content = [];
			/** @var $control IHtmlControl */
			if (($tag = $this->getTag()) === null) {
				foreach ($this->getControlList() as $control) {
					$content[] = $control->render();
				}
				return implode('', $content);
			}
			$content[] = $indent = str_repeat("\t", $this->node->getLevel());
			$content[] = '<' . $this->getTag();
			foreach ($this->getAttributeList() as $name => $list) {
				if (is_array($list)) {
					$content[] = ' ' . $name . '="' . implode(' ', $list) . '"';
					continue;
				}
				$content[] = ' ' . $name . '="' . $list . '"';
			}
			$pair = '>';
			$content[] = $pair;
			$newline = "\n";
			if (($value = $this->node->getValue()) !== null) {
				$newline = null;
				$indent = null;
				$content[] = $value;
			}
			if ($this->node->isLeaf() && $this->isPair() === true) {
				$newline = null;
				$indent = null;
			}
			$content[] = $newline;
			foreach ($this->getControlList() as $control) {
				$content[] = $control->render();
			}
			if ($this->isPair()) {
				$content[] = $indent . '</' . $this->getTag() . ">\n";
			}
			return implode('', $content);
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
