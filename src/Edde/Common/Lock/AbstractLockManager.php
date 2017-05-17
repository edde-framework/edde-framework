<?php
	declare(strict_types=1);

	namespace Edde\Common\Lock;

	use Edde\Api\Lock\ILock;
	use Edde\Api\Lock\ILockManager;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractLockManager extends Object implements ILockManager {
		use ConfigurableTrait;
		/**
		 * @var ILock[]
		 */
		protected $lockList = [];
	}
