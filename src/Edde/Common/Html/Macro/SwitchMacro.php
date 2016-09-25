<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Reflection\ReflectionUtils;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Switch support.
	 */
	class SwitchMacro extends AbstractHtmlMacro {
		/**
		 * @var ICryptEngine
		 */
		protected $cryptEngine;

		/**
		 * A programmer enters an elevator, wanting to go to the 12th floor.
		 *
		 * So, he pushes 1, then he pushes 2, and starts looking for the Enter...
		 */
		public function __construct() {
			parent::__construct('switch', false);
		}

		/**
		 * @param ICryptEngine $cryptEngine
		 */
		public function lazyCryptEngine(ICryptEngine $cryptEngine) {
			$this->cryptEngine = $cryptEngine;
		}

		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			/** @var $stack \SplStack */
			$stack = $compiler->getVariable(static::class, new \SplStack());
			$switch = str_replace('-', '_', $this->cryptEngine->guid());
			$this->write($compiler, sprintf('$switch_%s = %s;', $switch, $this->switch($this->attribute($macro, $compiler, 'src', false))), 5);
			$stack->push($switch);
			foreach ($macro->getNodeList() as $node) {
				$compiler->runtimeMacro($node);
			}
			$stack->pop();
		}

		/**
		 * @param string $src
		 *
		 * @return string
		 */
		protected function switch (string $src): string {
			$func = substr($src, -2) === '()';
			$src = str_replace('()', '', $src);
			$type = $src[0];
			$src = StringUtils::camelize(substr($src, 1), null, true);
			if ($func) {
				return sprintf('%s->%s()', self::$reference[$type], $src);
			}
			return sprintf('%s::getProperty(%s, %s)', ReflectionUtils::class, self::$reference[$type], var_export($src, true));
		}
	}
