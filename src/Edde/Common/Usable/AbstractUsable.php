<?php
	declare(strict_types = 1);

	namespace Edde\Common\Usable;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Usable\IUsable;
	use Edde\Common\AbstractObject;

	abstract class AbstractUsable extends AbstractObject implements IUsable, ILazyInject {
		use UsableTrait;
	}
