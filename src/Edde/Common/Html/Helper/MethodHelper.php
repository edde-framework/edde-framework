<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Helper;

	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\AbstractHelper;

	/**
	 * This helper will translate "standard" call format into methods reffering to root or current control; it also supports container call reference.
	 */
	class MethodHelper extends AbstractHelper {
		public function helper($value, ...$parameterList) {
			if ($value === null) {
				return null;
			}
			/**
			 * intentionall assigment
			 */
			if ($match = StringUtils::match($value, '~^(?<type>\.|@)(?<method>[a-z0-9-]+)\(\)$~', true, true)) {
				$control = [
					'.' => '$root',
					'@' => '$stack->top()',
				];
				return sprintf('%s->%s()', $control[$match['type']], StringUtils::camelize($match['method'], null, true));
			} else if ($match = StringUtils::match($value, '~^(?<class>(\\\\[a-zA-Z0-9_]+)+)::(?<method>[a-zA-Z_]+)\(\)$~', true, true)) {
				return sprintf('$this->container->create(%s)->%s()', var_export($match['class'], true), $match['method']);
			}
			return null;
		}
	}
