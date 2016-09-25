<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\ILazyInject;
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
	abstract class AbstractMacro extends AbstractObject implements IMacro, ILazyInject {
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
		public function __construct(string $name, $compile) {
			$this->name = $name;
			$this->compile = $compile;
		}

		/**
		 * @inheritdoc
		 */
		public function isRuntime(): bool {
			return $this->isCompile() === false || $this->compile === null;
		}

		/**
		 * @inheritdoc
		 */
		public function isCompile(): bool {
			return $this->compile === true || $this->compile === null;
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
		 *
		 * @return mixed|null|string
		 */
		public function extract(INode $macro, string $name = null, $default = null) {
			$name = $name ?: $this->getName();
			$attribute = $macro->getAttribute($name, $default);
			$macro->removeAttribute($name);
			return $attribute;
		}

		/**
		 * @inheritdoc
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * return attribute from the given macro; throws exception if the attribute is not present
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 * @param string|null $name
		 * @param bool $helper
		 *
		 * @return mixed
		 * @throws MacroException
		 */
		protected function attribute(INode $macro, ICompiler $compiler, string $name = null, bool $helper = true) {
			$name = $name ?: $this->getName();
			if (($attribute = $macro->getAttribute($name)) === null) {
				throw new MacroException(sprintf('Missing attribute [%s] in macro node [%s].', $name, $macro->getPath()));
			}
			return ($helper && $filter = $compiler->helper($macro, $attribute)) ? $filter : $attribute;
		}

		/**
		 * return attribute list
		 *
		 * @param INode $macro
		 * @param ICompiler $compiler
		 * @param callable|null $default
		 *
		 * @return array
		 */
		protected function getAttributeList(INode $macro, ICompiler $compiler, callable $default = null): array {
			$attributeList = [];
			foreach ($macro->getAttributeList() as $k => &$v) {
				$v = ($value = $compiler->helper($macro, $v)) !== null ? $value : ($default ? $default($v) : $v);
				$attributeList[$k] = $v;
			}
			unset($v);
			return $attributeList;
		}

		protected function prepare() {
		}
	}
