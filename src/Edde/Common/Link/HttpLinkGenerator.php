<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	class HttpLinkGenerator extends AbstractLinkGenerator {
		public function generate($generate, ...$parameterList) {
			if (strpos($generate, 'http') === false) {
				return null;
			}
			return $generate;
		}
	}
