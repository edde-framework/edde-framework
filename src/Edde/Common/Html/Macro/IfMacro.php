<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IHelper;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Reflection\ReflectionUtils;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\HelperSet;

	/**
	 * Condition macro support.
	 */
	class IfMacro extends AbstractHtmlMacro implements IHelper {
		/**
		 * @var ICryptEngine
		 */
		protected $cryptEngine;

		/**
		 * MS-DOS is like the US railroad system. It's there, but people just ignore it and find other ways of getting where they want to go.
		 */
		public function __construct() {
			parent::__construct('if', false);
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
		public function helper(INode $macro, ICompiler $compiler, $value, ...$parameterList) {
			/** @var $stack \SplStack */
			$stack = $compiler->getVariable(static::class);
			if ($value === null) {
				return null;
			} else if ($value === '?:') {
				return '$if_' . $stack->top();
			} else if ($match = StringUtils::match($value, '~\?:(?<jump>\$|\.|\d+)?(\->(?<call>[a-z0-9-]+\(\)))?~', true, true)) {
				$jump = $match['jump'] ?? 0;
				if ($jump === '$') {
					$jump = 1;
				} else if ($jump === '.') {
					$jump = $stack->count();
				}
				$loop = null;
				foreach ($stack as $loop) {
					if ($jump-- <= 0) {
						break;
					}
				}
				if ($loop === null) {
					throw new MacroException(sprintf('There are no loops for macro [%s].', $macro->getPath()));
				}
				return '$if_' . $loop . (isset($match['call']) ? '->' . StringUtils::camelize($match['call'], null, true) : '');
			}
			return null;
		}

		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			/** @var $stack \SplStack */
			$stack = $compiler->getVariable(static::class, new \SplStack());
			$if = str_replace('-', '_', $this->cryptEngine->guid());
			$this->write($compiler, sprintf('if($if_%s = %s) {', $if, $this->if($this->attribute($macro, $compiler, 'src', false))), 5);
			$stack->push($if);
			foreach ($macro->getNodeList() as $node) {
				$compiler->runtimeMacro($node);
			}
			$stack->pop();
			$this->write($compiler, '}', 5);
		}

		/**
		 * @param string $src
		 *
		 * @return string
		 */
		protected function if (string $src): string {
			$func = substr($src, -2) === '()';
			$src = str_replace('()', '', $src);
			$type = $src[0];
			$src = StringUtils::camelize(substr($src, 1), null, true);
			if ($func) {
				return sprintf('%s->%s()', self::$reference[$type], $src);
			}
			return sprintf('%s::getProperty(%s, %s)', ReflectionUtils::class, self::$reference[$type], var_export($src, true));
		}

		protected function prepare() {
			parent::prepare();
			$this->helperSet = new HelperSet();
			$this->helperSet->registerHelper($this);
		}
	}
