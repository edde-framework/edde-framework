<?php
	declare(strict_types = 1);

	namespace Edde\Api\Acl;

	interface IAcl {
		/**
		 * register resource for this acl list
		 *
		 * @param bool $grant
		 * @param string $resource
		 * @param \DateTime $from
		 * @param \DateTime|null $until
		 *
		 * @return IAcl
		 */
		public function register(bool $grant, string $resource = null, \DateTime $from = null, \DateTime $until = null): IAcl;

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
