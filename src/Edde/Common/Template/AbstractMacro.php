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

	/**
	 * Base macro for all template macros.
	 */
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

		/**
		 * A master was explaining the nature of Tao to one of his novices. "The Tao is embodied in all software--regardless of how insignificant," said the master.
		 *
		 * "Is the Tao in the Unix command line?" asked the novice.
		 *
		 * "It is difficult to find, young one, but it is certainly there." came the reply.
		 *
		 * "Is Tao in a hand-held calculator?" asked the novice.
		 *
		 * "It is," came the reply.
		 *
		 * "Is the Tao in a video game?" continued the novice.
		 *
		 * "It is even in a video game," said the master.
		 *
		 * "What about MS-DOS?"
		 *
		 * The master coughed and shifted his position slightly. "The lesson is over for today," he said.
		 *
		 * @param string $name
		 * @param bool $compile
		 */
		public function __construct(string $name, bool $compile) {
			$this->name = $name;
			$this->compile = $compile;
		}

		/**
		 * @inheritdoc
		 */
		public function isRuntime(): bool {
			return $this->isCompile() === false;
		}

		/**
		 * @inheritdoc
		 */
		public function isCompile(): bool {
			return $this->compile === true;
		}

		/**
		 * @inheritdoc
		 */
		public function hasHelperSet(): bool {
			$this->use();
			return $this->helperSet !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function getHelperSet(): IHelperSet {
			$this->use();
			return $this->helperSet;
		}

		/**
		 * extract an attribute and remove it from attribute list
		 *
		 * @param INode $macro
		 * @param string $name
		 * @param null $default
		 * @param bool $helper
		 *
		 * @return mixed|null|string
		 */
		public function extract(INode $macro, string $name, $default = null, bool $helper = true) {
			$attribute = $macro->getAttribute($name, $default);
			$macro->removeAttribute($name);
			return ($helper && $filter = $this->compiler->helper($attribute)) ? $filter : $attribute;
		}

		public function macro(INode $macro, ICompiler $compiler) {
			$this->macro = $macro;
			$this->compiler = $compiler;
			return $this->onMacro();
		}

		abstract protected function onMacro();

		protected function attribute(string $name = null, bool $helper = true) {
			$name = $name ?: $this->getName();
			if (($attribute = $this->macro->getAttribute($name)) === null) {
				throw new MacroException(sprintf('Missing attribute [%s] in macro node [%s].', $name, $this->macro->getPath()));
			}
			return ($helper && $filter = $this->compiler->helper($attribute)) ? $filter : $attribute;
		}

		public function getName(): string {
			return $this->name;
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
