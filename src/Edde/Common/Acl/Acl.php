<?php
	declare(strict_types = 1);

	namespace Edde\Common\Acl;

	use Edde\Api\Acl\IAcl;
	use Edde\Common\AbstractObject;

	class Acl extends AbstractObject implements IAcl {
		public function can(string $resource, \DateTime $dateTime = null): bool {
			return false;
		}
	}
