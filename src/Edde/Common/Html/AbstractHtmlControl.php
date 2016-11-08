<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Crypt\LazyCryptEngineTrait;
	use Edde\Api\File\FileException;
	use Edde\Api\File\LazyTempDirectoryTrait;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Web\LazyJavaScriptCompilerTrait;
	use Edde\Api\Web\LazyStyleSheetCompilerTrait;
	use Edde\Common\Control\AbstractControl;
	use Edde\Common\File\File;

	/**
	 * Base class for all html based controls.
	 */
	abstract class AbstractHtmlControl extends AbstractControl implements IHtmlControl {
		use LazyContainerTrait;
		use LazyJavaScriptCompilerTrait;
		use LazyStyleSheetCompilerTrait;
		use LazyTempDirectoryTrait;
		use LazyCryptEngineTrait;

		public function setTag(string $tag, bool $pair = true): IHtmlControl {
			$this->use();
			$this->node->addMetaList([
				'tag' => $tag,
				'pair' => $pair,
			]);
			return $this;
		}

		public function setId(string $id) {
			$this->use();
			$this->setAttribute('id', $id);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setAttribute($attribute, $value) {
			$this->use();
			/** @noinspection DegradedSwitchInspection */
			switch ($attribute) {
				case 'class':
					$this->addAttribute($attribute, $value);
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
			$this->use();
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
			$javascript = $this->tempDirectory->save(sha1($guid . '-js') . '.js', $source = $javascript->get());
			$javascript->save(sprintf("Edde.Utils.class('." . $selector . "', %s);", $source));
			$this->javaScriptCompiler->addResource($javascript);
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
			$this->styleSheetCompiler->addResource($this->tempDirectory->save(sha1(static::class . '-css') . '.css', $stylesheet->get()));
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getId(): string {
			$this->use();
			return $this->getAttribute('id', '');
		}

		/**
		 * @inheritdoc
		 */
		public function getAttribute(string $name, $default = '') {
			$this->use();
			return $this->node->getAttribute($name, $default);
		}

		/**
		 * @inheritdoc
		 */
		public function setText(string $text) {
			$this->use();
			$this->node->setValue($text);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function addAttributeList(array $attributeList): IHtmlControl {
			$this->use();
			$this->node->addAttributeList($attributeList);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setAttributeList(array $attributeList): IHtmlControl {
			$this->use();
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
			$this->use();
			return $this->node->hasAttribute($attribute);
		}

		public function toggleClass(string $class, bool $enable = null) {
			$this->use();
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
			$this->use();
			return in_array($class, $this->getClassList(), true);
		}

		/**
		 * @inheritdoc
		 */
		public function getClassList() {
			$this->use();
			return $this->getAttribute('class', []);
		}

		/**
		 * @inheritdoc
		 */
		public function addClass(string $class) {
			$this->addAttribute('class', $class);
			return $this;
		}

		public function removeClass(string $class) {
			$this->use();
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
			$this->use();
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
			$pair = '>';
			$content[] = $pair;
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
			$this->use();
			return $this->node->getMeta('tag');
		}

		/**
		 * @inheritdoc
		 */
		public function getAttributeList(): array {
			$this->use();
			return $this->node->getAttributeList();
		}

		/**
		 * @inheritdoc
		 */
		public function isPair(): bool {
			$this->use();
			return $this->node->getMeta('pair', true);
		}

		protected function placeholder(string $id) {
			return $this->addControl($this->createControl(PlaceholderControl::class)
				->setId($id)
				->dirty());
		}

		public function createControl(string $control, ...$parameterList): IHtmlControl {
			return $this->container->create($control, ...$parameterList);
		}
	}
