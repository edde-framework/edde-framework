<?php
	declare(strict_types = 1);

	namespace Edde\Common\Acl;

	use Edde\Api\Acl\AclException;
	use Edde\Api\Acl\IAcl;
	use Edde\Api\Acl\IAclManager;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Common\Deffered\AbstractDeffered;

	class AclManager extends AbstractDeffered implements IAclManager {
		use LazyContainerTrait;
		/**
		 * array of acl rules
		 *
		 * @var array
		 */
		protected $aclList = [];

		public function access(string $group, bool $grant, string $resource = null, \DateTime $until = null): IAclManager {
			return $this;
		}

		public function grant(string $group, string $resource = null, \DateTime $until = null): IAclManager {
			return $this->access($group, true, $resource, $until);
		}

		public function deny(string $group, string $resource = null, \DateTime $until = null): IAclManager {
			return $this->access($group, false, $resource, $until);
		}

		public function acl(array $groupList): IAcl {
			if ($diff = array_diff($groupList, array_keys($this->aclList))) {
				throw new AclException(sprintf('Unknown group [%s]. Did you register access for this group(s)?', implode(', ', $diff)));
			}
			return $this->container->inject(new Acl());
		}
	}
