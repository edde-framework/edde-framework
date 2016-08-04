<?php
	declare(strict_types = 1);

	namespace Edde\Common\Cache;

	use Edde\Api\Cache\IInvalidator;
	use Edde\Common\AbstractObject;

	abstract class AbstractInvalidator extends AbstractObject implements IInvalidator {
	}
