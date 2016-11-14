<?php
	declare(strict_types = 1);

	namespace Edde\Common\Acl;

	use Edde\Api\Acl\AclException;
	use Edde\Api\Acl\IAcl;
	use Edde\Common\AbstractObject;

	class Acl extends AbstractObject implements IAcl {
		/**
		 * @var array
		 */
		protected $aclList = [];

		public function register(bool $grant, string $resource = null, \DateTime $until = null): IAcl {
			$this->aclList[$resource][] = [
				$grant,
				$until,
			];
			return $this;
		}

		public function can(string $resource, \DateTime $dateTime = null): bool {
			if (isset($this->aclList[$resource]) === false && isset($this->aclList[null]) === false) {
				throw new AclException(sprintf('Asking for unknown resource [%s].', $resource));
			}
			$can = false;
			$dateTime = $dateTime ?: new \DateTime();
			foreach ($this->aclList[$resource] ?? $this->aclList[null] as $rule) {
				/** @var $until \DateTime */
				list($grant, $until) = $rule;
				if ($until && $grant = ($diff = $until->diff($dateTime)) && $diff->invert === 0) {
					continue;
				}
				$can = $grant;
			}
			return $can;
		}
	}
