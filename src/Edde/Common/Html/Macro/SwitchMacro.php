<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Crypt\LazyCryptEngineTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Node\Node;
	use Edde\Common\Reflection\ReflectionUtils;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Switch support.
	 */
	class SwitchMacro extends AbstractHtmlMacro {
		use LazyCryptEngineTrait;

		/**
		 * A programmer enters an elevator, wanting to go to the 12th floor.
		 *
		 * So, he pushes 1, then he pushes 2, and starts looking for the Enter...
		 */
		public function __construct() {
			parent::__construct('switch');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function compileInline(INode $macro, ICompiler $compiler) {
			$macro->switch(new Node('switch', null, ['src' => $this->extract($macro, self::COMPILE_PREFIX . $this->getName())]));
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			/** @var $stack \SplStack */
			$stack = $compiler->getVariable(static::class, new \SplStack());
			$switch = str_replace('-', '_', $this->cryptEngine->guid());
			$this->write($macro, $compiler, sprintf('$switch_%s = %s;', $switch, $this->switch($macro, $this->attribute($macro, $compiler, 'src', false))), 5);
			$stack->push($switch);
			parent::macro($macro, $compiler);
			$stack->pop();
		}

		/**
		 * @param INode $macro
		 * @param string $src
		 *
		 * @return string
		 * @throws MacroException
		 */
		protected function switch (INode $macro, string $src): string {
			$func = substr($src, -2) === '()';
			$src = str_replace('()', '', $src);
			$type = $src[0];
			$src = StringUtils::camelize(substr($src, 1), null, true);
			if ($func) {
				return sprintf('%s->%s()', $this->reference($macro, $type), $src);
			}
			return sprintf('%s::getProperty(%s, %s)', ReflectionUtils::class, $this->reference($macro, $type), var_export($src, true));
		}
	}
