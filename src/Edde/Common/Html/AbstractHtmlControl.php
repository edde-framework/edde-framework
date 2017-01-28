<?php
	declare(strict_types=1);

	namespace Edde\Common\Html;

	use Edde\Api\Control\ControlException;
	use Edde\Api\Control\IControl;
	use Edde\Api\Crypt\LazyCryptEngineTrait;
	use Edde\Api\File\FileException;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Web\LazyJavaScriptCompilerTrait;
	use Edde\Api\Web\LazyStyleSheetCompilerTrait;
	use Edde\Common\Control\AbstractControl;
	use Edde\Common\File\File;
	use Edde\Common\Node\NodeQuery;
	use Edde\Common\Strings\StringResource;

	/**
	 * Base class for all html based controls.
	 */
	abstract class AbstractHtmlControl extends AbstractControl implements IHtmlControl {
		use LazyJavaScriptCompilerTrait;
		use LazyStyleSheetCompilerTrait;
		use LazyCryptEngineTrait;

		public function setTag(string $tag, bool $pair = true): IHtmlControl {
			$this->node->addMetaList([
				'tag' => $tag,
				'pair' => $pair,
			]);
			return $this;
		}

		public function setId(string $id) {
			$this->setAttribute('id', $id);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setAttribute($attribute, $value) {
			/** @noinspection DegradedSwitchInspection */
			switch ($attribute) {
				case 'class':
					$this->addClass($value);
					break;
				default:
					$this->node->setAttribute($attribute, $value);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function addAttribute(string $attribute, $value) {
			$attributeList = $this->node->getAttributeList();
			$attributeList[$attribute][] = $value;
			$this->node->setAttributeList($attributeList);
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function javascript(string $class, string $file = null): IHtmlControl {
			$selector = 'guid-' . ($guid = $this->cryptEngine->guid());
			$this->addClass($selector);
			$javascript = null;
			if (class_exists($class)) {
				$reflectionClass = new \ReflectionClass($class);
				$javascript = new File(str_replace('.php', '.js', $reflectionClass->getFileName()));
			}
			if ($file !== null) {
				$javascript = new File($file);
			}
			$this->javaScriptCompiler->addResource(new StringResource(sprintf("Edde.Utils.class('." . $selector . "', %s);", $javascript->get())));
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws FileException
		 */
		public function stylesheet(string $file = null): IHtmlControl {
			$reflectionClass = new \ReflectionClass($this);
			$stylesheet = new File(str_replace('.php', '.css', $reflectionClass->getFileName()));
			if ($file !== null) {
				$stylesheet = new File($file);
			}
			$this->styleSheetCompiler->addResource($stylesheet);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getId(): string {
			return $this->getAttribute('id', '');
		}

		/**
		 * @inheritdoc
		 */
		public function getAttribute(string $name, $default = '') {
			return $this->node->getAttribute($name, $default);
		}

		/**
		 * @inheritdoc
		 */
		public function setText(string $text) {
			$this->node->setValue($text);
			return $this;
		}

		public function getText(): string {
			return $this->node->getValue('');
		}

		/**
		 * @inheritdoc
		 */
		public function addAttributeList(array $attributeList): IHtmlControl {
			$this->node->addAttributeList($attributeList);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setAttributeList(array $attributeList): IHtmlControl {
			/**
			 * intentional loop, because control can simply alter attributes
			 */
			foreach ($attributeList as $name => $value) {
				$this->setAttribute($name, $value);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasAttribute($attribute) {
			return $this->node->hasAttribute($attribute);
		}

		public function toggleClass(string $class, bool $enable = null): IHtmlControl {
			$hasClass = $this->hasClass($class);
			if ($enable === null) {
				if ($hasClass === false) {
					$this->addClass($class);
					return $this;
				}
				$this->removeClass($class);
			} else if ($enable && $hasClass === false) {
				$this->addClass($class);
			} else if ($enable === false && $hasClass === true) {
				$this->removeClass($class);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasClass(string $class) {
			return in_array($class, $this->getClassList(), true);
		}

		/**
		 * @inheritdoc
		 */
		public function getClassList() {
			return $this->getAttribute('class', []);
		}

		/**
		 * @inheritdoc
		 */
		public function addClass(string $class) {
			foreach (explode(' ', $class) as $value) {
				if (($value = trim($value)) !== '') {
					$this->addAttribute('class', $value);
				}
			}
			return $this;
		}

		public function removeClass(string $class) {
			$diff = array_diff($this->getClassList(), [$class]);
			$this->node->removeAttribute('class');
			if (empty($diff) === false) {
				$this->node->setAttribute('class', $diff);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function render(int $indent = 0): string {
			$content = [];
			/** @var $control IHtmlControl */
			if (($tag = $this->getTag()) === null) {
				foreach ($this->getControlList() as $control) {
					$content[] = $control->render(-1);
				}
				return implode('', $content);
			}
			$content[] = $indentation = str_repeat("\t", $this->node->getLevel() + $indent);
			$content[] = '<' . $this->getTag();
			foreach ($this->getAttributeList() as $name => $list) {
				if (is_array($list)) {
					$content[] = ' ' . $name . '="' . implode(' ', $list) . '"';
					continue;
				}
				$content[] = ' ' . $name . '="' . $list . '"';
			}
			$content[] = '>';
			$newline = "\n";
			if (($value = $this->node->getValue()) !== null) {
				$newline = null;
				$indentation = null;
				$content[] = $value;
			}
			if ($this->node->isLeaf() && $this->isPair() === true) {
				$newline = null;
				$indentation = null;
			}
			$content[] = $newline;
			foreach ($this->getControlList() as $control) {
				$content[] = $control->render($indent);
			}
			if ($this->isPair()) {
				$content[] = $indentation . '</' . $this->getTag() . ">\n";
			}
			return implode('', $content);
		}

		/**
		 * @inheritdoc
		 */
		public function getTag(): string {
			return $this->node->getMeta('tag');
		}

		/**
		 * @inheritdoc
		 */
		public function getAttributeList(): array {
			return $this->node->getAttributeList();
		}

		public function data(string $name, $data): IHtmlControl {
			$this->setAttribute('data-' . $name, $data);
			return $this;
		}

		public function getData(string $name, $default = null) {
			return $this->getAttribute('data-' . $name, $default);
		}

		/**
		 * @inheritdoc
		 */
		public function isPair(): bool {
			return $this->node->getMeta('pair', true);
		}

		/**
		 * @inheritdoc
		 */
		public function remove(string $id): IHtmlControl {
			foreach (NodeQuery::node($this->getNode(), '/**/[id]') as $node) {
				if ($node->getAttribute('id') === $id) {
					$node->getParent()
						->removeNode($node);
				}
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function replace(IHtmlControl $htmlControl): IHtmlControl {
			if (($id = $htmlControl->getId()) === null) {
				throw new ControlException(sprintf('Cannot replace control [%s] without id.', get_class($htmlControl)));
			}
			foreach (NodeQuery::node($this->getNode(), '/**/[id]') as $node) {
				if ($node->getAttribute('id') === $id) {
					$node->setMeta('control', $htmlControl);
					break;
				}
			}
			return $this;
		}

		protected function placeholder(string $id) {
			return $this->addControl($this->createControl(PlaceholderControl::class)
				->setId($id)
				->dirty());
		}

		/**
		 * @inheritdoc
		 *
		 * @return IHtmlControl|IControl
		 */
		public function createControl(string $control, ...$parameterList): IControl {
			return parent::createControl($control, ...$parameterList);
		}
	}
