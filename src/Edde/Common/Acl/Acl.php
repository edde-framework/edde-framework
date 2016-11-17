<?php
	declare(strict_types = 1);

	namespace Edde\Common\Acl;

	use Edde\Api\Acl\AclException;
	use Edde\Api\Acl\IAcl;
	use Edde\Common\AbstractObject;

	class Acl extends AbstractObject implements IAcl {
		/**
		 * @var array[]
		 */
		protected $aclList = [];

		/**
		 * @inheritdoc
		 */
		public function register(bool $grant, string $resource = null, \DateTime $from = null, \DateTime $until = null): IAcl {
			$this->aclList[$resource][] = [
				$grant,
				$from,
				$until,
			];
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws AclException
		 */
		public function can(string $resource, \DateTime $dateTime = null): bool {
			if (isset($this->aclList[$resource]) === false && isset($this->aclList[null]) === false) {
				throw new AclException(sprintf('Asking for unknown resource [%s].', $resource));
			}
			$can = false;
			/** @noinspection UnnecessaryParenthesesInspection */
			$stamp = ($dateTime ?: new \DateTime())->getTimestamp();
			/** @var $from \DateTime */
			/** @var $until \DateTime */
			foreach ($this->aclList[$resource] ?? $this->aclList[null] as list($grant, $from, $until)) {
				if (($from && $stamp < $from->getTimestamp()) || ($until && $stamp > $until->getTimestamp())) {
					continue;
				}
				$can = $grant;
			}
			return $can;
		}
	}