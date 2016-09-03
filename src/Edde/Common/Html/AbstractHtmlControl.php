<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Control\AbstractControl;
	use Edde\Common\File\File;

	abstract class AbstractHtmlControl extends AbstractControl implements IHtmlControl {
		use LazyInjectTrait;
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

		public function injectContainer(IContainer $container) {
			$this->container = $container;
		}

		public function lazyJavaScriptCompiler(IJavaScriptCompiler $javaScriptCompiler) {
			$this->javaScriptCompiler = $javaScriptCompiler;
		}

		public function lazyStyleSheetCompiler(IStyleSheetCompiler $styleSheetCompiler) {
			$this->styleSheetCompiler = $styleSheetCompiler;
		}

		public function setTag(string $tag, bool $pair = true) {
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

		public function setAttribute($attribute, $value) {
			$this->use();
			switch ($attribute) {
				case 'class':
					$this->addAttribute($attribute, $value);
					break;
				default:
					$this->node->setAttribute($attribute, $value);
			}
			return $this;
		}

		public function addAttribute($attribute, $value) {
			$this->use();
			$attributeList = $this->getAttributeList();
			$attributeList[$attribute][] = $value;
			$this->node->setAttributeList($attributeList);
			return $this;
		}

		public function getAttributeList(): array {
			$this->use();
			return $this->node->getAttributeList();
		}

		public function client() {
			$this->setAttribute('data-class', str_replace('\\', '.', static::class));
			$reflectionClass = new \ReflectionClass($this);
			$javaScript = new File(str_replace('.php', '.js', $reflectionClass->getFileName()));
			if ($javaScript->isAvailable()) {
				$this->javaScriptCompiler->addResource($javaScript);
			}
			$styleSheet = new File(str_replace('.php', '.css', $reflectionClass->getFileName()));
			if ($styleSheet->isAvailable()) {
				$this->styleSheetCompiler->addResource($styleSheet);
			}
			return $this;
		}

		public function getId(): string {
			$this->use();
			return $this->getAttribute('id', '');
		}

		public function getAttribute(string $name, string $default = '') {
			return $this->node->getAttribute($name, $default);
		}

		public function setText(string $text) {
			$this->use();
			$this->node->setValue($text);
			return $this;
		}

		public function addAttributeList(array $attributeList) {
			$this->use();
			$this->node->addAttributeList($attributeList);
			return $this;
		}

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

		public function hasAttribute($attribute) {
			return $this->node->hasAttribute($attribute);
		}

		public function addClass($class) {
			$this->addAttribute('class', $class);
			return $this;
		}

		public function hasClass($class) {
			$classList = $this->getClassList();
			return isset($classList[$class]);
		}

		public function getClassList() {
			return $this->getAttribute('class');
		}

		public function render() {
			$this->use();
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
				if ($control->isDirty()) {
					$content[] = $control->render();
				}
			}
			if ($this->isPair()) {
				$content[] = $indent . '</' . $this->getTag() . ">\n";
			}
			return implode('', $content);
		}

		public function getTag() {
			$this->use();
			return $this->node->getMeta('tag');
		}

		public function isPair() {
			$this->use();
			return $this->node->getMeta('pair', true);
		}

		public function createControl(string $control, ...$parameterList): IHtmlControl {
			return $this->container->create($control, ...$parameterList);
		}
	}
