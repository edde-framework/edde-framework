<?php
	declare(strict_types = 1);

	namespace Edde\Api\Acl;

	interface IAcl {
		/**
		 * check if the given right is granted for this acl
		 *
		 * @param string $resource
		 * @param \DateTime|null $dateTime
		 *
		 * @return bool
		 */
		public function can(string $resource, \DateTime $dateTime = null): bool;
	}
