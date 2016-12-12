<?php
	declare(strict_types = 1);

	namespace Edde\Common;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Deffered\IDeffered;

	abstract class AbstractObject implements IDeffered {
		use ObjectTrait;
	}
