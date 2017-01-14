<?php
	declare(strict_types=1);

	namespace Edde\Service\Acl;

	use Edde\Api\Acl\AclException;
	use Edde\Api\Acl\IAcl;
	use Edde\Api\Acl\IAclManager;
	use Edde\Common\Acl\Acl;
	use Edde\Common\Object;

	class AclManager extends Object implements IAclManager {
		/**
		 * array of acl rules
		 *
		 * @var array[]
		 */
		protected $aclList = [];

		public function access(string $group, bool $grant, string $resource = null, \DateTime $from = null, \DateTime $until = null): IAclManager {
			$this->aclList[$group][] = [
				$grant,
				$resource,
				$from,
				$until,
			];
			return $this;
		}

		public function grant(string $group, string $resource = null, \DateTime $from = null, \DateTime $until = null): IAclManager {
			return $this->access($group, true, $resource, $from, $until);
		}

		public function deny(string $group, string $resource = null, \DateTime $from = null, \DateTime $until = null): IAclManager {
			return $this->access($group, false, $resource, $from, $until);
		}

		public function acl(array $groupList): IAcl {
			if ($diff = array_diff($groupList, array_keys($this->aclList))) {
				throw new AclException(sprintf('Unknown group [%s]. Did you register access for this group(s)?', implode(', ', $diff)));
			}
			$acl = new Acl();
			foreach ($groupList as $group) {
				foreach ($this->aclList[$group] as $rule) {
					call_user_func_array([
						$acl,
						'register',
					], $rule);
				}
			}
			return $acl;
		}
	}
