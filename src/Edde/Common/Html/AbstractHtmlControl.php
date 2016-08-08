<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Common\Control\AbstractControl;

	abstract class AbstractHtmlControl extends AbstractControl implements IHtmlControl {
		/**
		 * @var IContainer
		 */
		protected $container;

		public function injectContainer(IContainer $container) {
			$this->container = $container;
		}

		public function setTag(string $tag, bool $pair = true) {
			$this->usse();
			$this->node->addMetaList([
				'tag' => $tag,
				'pair' => $pair,
			]);
			return $this;
		}

		public function setId(string $id) {
			$this->usse();
			$this->setAttribute('id', $id);
			return $this;
		}

		public function setAttribute($attribute, $value) {
			$this->usse();
			$this->node->setAttribute($attribute, $value);
			return $this;
		}

		public function getId(): string {
			$this->usse();
			return $this->getAttribute('id', '');
		}

		public function getAttribute(string $name, string $default = '') {
			return $this->node->getAttribute($name, $default);
		}

		public function setText(string $text) {
			$this->usse();
			$this->node->setValue($text);
			return $this;
		}

		public function addAttributeList(array $attributeList) {
			$this->usse();
			$this->node->addAttributeList($attributeList);
			return $this;
		}

		public function setAttributeList(array $attributeList) {
			$this->usse();
			$this->node->setAttributeList($attributeList);
			return $this;
		}

		public function hasAttribute($attribute) {
			return $this->node->hasAttribute($attribute);
		}

		public function addClass($class) {
			$this->addAttribute('class', $class);
			return $this;
		}

		public function addAttribute($attribute, $value) {
			$this->usse();
			$attributeList = $this->getAttribute('class');
			$attributeList[] = $value;
			$this->node->setAttribute('class', $attributeList);
			return $this;
		}

		public function hasClass($class) {
			$classList = $this->getClassList();
			return isset($classList[$class]);
		}

		public function getClassList() {
			return $this->getAttribute('class');
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
			return $this->node->getMeta('tag');
		}

		public function getAttributeList(): array {
			$this->usse();
			return $this->node->getAttributeList();
		}

		public function isPair() {
			$this->usse();
			return $this->node->getMeta('pair', true);
		}

		/**
		 * @param string $control
		 * @param array ...$parameterList
		 *
		 * @return IHtmlControl
		 */
		protected function createControl($control, ...$parameterList) {
			return $this->container->create($control, ...$parameterList);
		}
	}
