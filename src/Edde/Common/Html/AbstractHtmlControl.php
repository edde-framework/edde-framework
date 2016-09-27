<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\File\FileException;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Control\AbstractControl;
	use Edde\Common\File\File;

	abstract class AbstractHtmlControl extends AbstractControl implements IHtmlControl {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var IJavaScriptCompiler
		 */
		protected $javaScriptCompiler;
		/**
		 * @var IStyleSheetCompiler
		 */
		protected $styleSheetCompiler;
		/**
		 * @var ITempDirectory
		 */
		protected $tempDirectory;

		public function injectContainer(IContainer $container) {
			$this->container = $container;
		}

		public function lazyJavaScriptCompiler(IJavaScriptCompiler $javaScriptCompiler) {
			$this->javaScriptCompiler = $javaScriptCompiler;
		}

		public function lazyStyleSheetCompiler(IStyleSheetCompiler $styleSheetCompiler) {
			$this->styleSheetCompiler = $styleSheetCompiler;
		}

		public function lazyTempDirectory(ITempDirectory $tempDirectory) {
			$this->tempDirectory = $tempDirectory;
		}

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
			$this->setAttribute('data-class', $class = str_replace('\\', '.', $class ?: static::class));
			$reflectionClass = new \ReflectionClass($this);
			$javascript = new File(str_replace('.php', '.js', $reflectionClass->getFileName()));
			if ($file !== null) {
				$javascript = new File($file);
			}
			$javascript = $this->tempDirectory->save(sha1($class . '-js') . '.js', $source = $javascript->get());
			$javascript->save(sprintf("Edde.Utils.class('" . $class . "', %s);", $source));
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
			$content[] = $indentantion = str_repeat("\t", $this->node->getLevel() + $indent);
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
				$indentantion = null;
				$content[] = $value;
			}
			if ($this->node->isLeaf() && $this->isPair() === true) {
				$newline = null;
				$indentantion = null;
			}
			$content[] = $newline;
			foreach ($this->getControlList() as $control) {
				if ($control->isDirty()) {
					$content[] = $control->render($indent);
				}
			}
			if ($this->isPair()) {
				$content[] = $indentantion . '</' . $this->getTag() . ">\n";
			}
			return implode('', $content);
		}

		/**
		 * @inheritdoc
		 */
		public function getTag() {
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
			$this->addControl($this->createControl(PlaceholderControl::class)
				->setId($id)
				->dirty());
			return $this;
		}

		public function createControl(string $control, ...$parameterList): IHtmlControl {
			return $this->container->create($control, ...$parameterList);
		}
	}
