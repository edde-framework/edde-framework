<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Identity\IAuthManager;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractAuthManager extends AbstractUsable implements IAuthManager {
	}
