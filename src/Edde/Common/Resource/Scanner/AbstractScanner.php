<?php
	declare(strict_types = 1);

	namespace Edde\Common\Resource\Scanner;

	use Edde\Api\Resource\Scanner\IScanner;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractScanner extends AbstractUsable implements IScanner {
		protected function prepare() {
		}
	}
