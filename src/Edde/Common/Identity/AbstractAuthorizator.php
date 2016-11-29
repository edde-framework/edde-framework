<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Identity\IAuthorizator;
	use Edde\Common\AbstractObject;

	/**
	 * Common stuff for an authorizator implementations.
	 */
	abstract class AbstractAuthorizator extends AbstractObject implements IAuthorizator {
	}
