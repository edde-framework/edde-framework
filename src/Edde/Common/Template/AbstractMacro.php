<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IHelperSet;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\MacroException;
	use Edde\Common\AbstractObject;
	use Edde\Common\Usable\UsableTrait;

	abstract class AbstractMacro extends AbstractObject implements IMacro {
		use UsableTrait;
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var bool
		 */
		protected $compile;
		/**
		 * @var INode
		 */
		protected $macro;
		/**
		 * @var ICompiler
		 */
		protected $compiler;
		/**
		 * @var IHelperSet
		 */
		protected $helperSet;

		public function __construct(string $name, bool $compile) {
			$this->name = $name;
			$this->compile = $compile;
		}

		public function getName(): string {
			return $this->name;
		}

		public function isRuntime(): bool {
			return $this->isCompile() === false;
		}

		public function isCompile(): bool {
			return $this->compile === true;
		}

		public function hasHelperSet(): bool {
			$this->use();
			return $this->helperSet !== null;
		}

		public function getHelperSet(): IHelperSet {
			$this->use();
			return $this->helperSet;
		}

		public function extract(INode $macro, string $name, $default = null) {
			$attribute = $macro->getAttribute($name, $default);
			$macro->removeAttribute($name);
			return $attribute;
		}

		public function macro(INode $macro, ICompiler $compiler) {
			$this->macro = $macro;
			$this->compiler = $compiler;
			return $this->onMacro();
		}

		abstract protected function onMacro();

		protected function attribute(string $name, bool $helper = true) {
			if (($attribute = $this->macro->getAttribute($name)) === null) {
				throw new MacroException(sprintf('Missing attribute [%s] in macro node [%s].', $name, $this->macro->getPath()));
			}
			return ($helper && $filter = $this->compiler->helper($attribute)) ? $filter : $attribute;
		}

		protected function getAttributeList(callable $default = null): array {
			$attributeList = [];
			foreach ($this->macro->getAttributeList() as $k => &$v) {
				$v = ($value = $this->compiler->helper($v)) !== null ? $value : ($default ? $default($v) : $v);
				$attributeList[$k] = $v;
			}
			unset($v);
			return $attributeList;
		}

		protected function prepare() {
		}
	}
