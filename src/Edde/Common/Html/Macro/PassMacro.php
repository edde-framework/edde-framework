<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Node\Node;
	use Edde\Common\Reflection\ReflectionUtils;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Inline pass macro will generate pass macros which will execute "final" pass.
	 */
	class PassMacro extends AbstractHtmlMacro {
		/**
		 * Hardware: "A product that if you play with it long enough, breaks."
		 *
		 * Software: "A product that if you play with it long enough, it works."
		 */
		public function __construct() {
			parent::__construct('pass');
		}

		/**
		 * @inheritdoc
		 */
		public function compileInline(INode $macro, ICompiler $compiler) {
			$macro->prepend(new Node('pass', null, ['target' => $this->extract($macro, 't:' . $this->getName())]));
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 * @throws MacroException
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$target = $this->attribute($macro, $compiler, 'target', false);
			$func = substr($target, -2) === '()';
			$target = str_replace('()', '', $target);
			$type = $target[0];
			$target = StringUtils::camelize(substr($target, 1), null, true);
			if ($func === false) {
				$this->write($compiler, sprintf('%s::setProperty(%s, %s, $control);', ReflectionUtils::class, self::$reference[$type], var_export($target, true)), 5);
				return;
			}
			$this->write($compiler, sprintf('%s->%s($control);', self::$reference[$type], $target), 5);
		}
	}
