<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Helper;

	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\AbstractHelper;

	class MethodHelper extends AbstractHelper {
		public function helper($value, ...$parameterList) {
			if ($value === null) {
				return null;
			}
			if ($match = StringUtils::match($value, '~^(?<type>\.|@)(?<method>[a-z0-9-]+)\(\)$~', true, true)) {
				$control = [
					'.' => '$root',
					'@' => '$control',
				];
				return sprintf('%s->%s()', $control[$match['type']], StringUtils::camelize($match['method'], null, true));
			}
			return null;
		}
	}
