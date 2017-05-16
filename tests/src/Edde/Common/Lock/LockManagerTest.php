<?php
	declare(strict_types=1);

	namespace Edde\Common\Lock;

	use Edde\Api\Lock\LazyLockManagerTrait;
	use Edde\Ext\Test\TestCase;

	class LockManagerTest extends TestCase {
		use LazyLockManagerTrait;

		public function testLocking() {
			// $this->lockManager->
		}
	}
