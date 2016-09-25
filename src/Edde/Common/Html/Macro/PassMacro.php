<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

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
			parent::__construct('pass', false);
		}

		protected function onMacro() {
			$target = $this->attribute('target', false);
			$func = substr($target, -2) === '()';
			$target = str_replace('()', '', $target);
			$type = $target[0];
			$target = var_export(StringUtils::camelize(substr($target, 1), null, true), true);
			if ($type === '.') {
				$this->write(sprintf('%s::setProperty($root, %s, $control);', ReflectionUtils::class, $target), 5);
			} else if ($type === '@') {
				$this->write(sprintf('%s::setProperty($control, %s, $control);', ReflectionUtils::class, $target), 5);
			} else if ($type === ':') {
				$this->write(sprintf('%s::setProperty($control->getRoot(), %s, $control);', ReflectionUtils::class, $target), 5);
			}
		}
	}
