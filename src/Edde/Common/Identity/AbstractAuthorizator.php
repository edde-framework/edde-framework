<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Identity\IAuthorizator;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractAuthorizator extends AbstractUsable implements IAuthorizator {
	}
