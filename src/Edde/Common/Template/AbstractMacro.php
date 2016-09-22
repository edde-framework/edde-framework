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
		protected $helperSet;

		public function __construct(string $name, bool $compile = true) {
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

		public function attribute(INode $macro, string $name) {
			if (($attribute = $macro->getAttribute($name)) === null) {
				throw new MacroException(sprintf('Missing attribute [%s] in macro node [%s].', $name, $macro->getPath()));
			}
			return $attribute;
		}

		public function extract(INode $macro, string $name) {
			$attribute = $macro->getAttribute($name);
			$macro->removeAttribute($name);
			return $attribute;
		}

		public function macro(INode $macro, ICompiler $compiler) {
			$this->macro = $macro;
			$this->compiler = $compiler;
			return $this->onMacro();
		}

		abstract protected function onMacro();

		protected function prepare() {
		}
	}
