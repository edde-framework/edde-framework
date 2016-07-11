<?php
	namespace Edde\Common\Upgrade;

	use Edde\Api\Upgrade\IUpgrade;
	use Edde\Api\Upgrade\UpgradeException;
	use phpunit\framework\TestCase;

	class UpgradeManagerTest extends TestCase {
		public function testCommon() {
			$version = 0;
			$upgradeManager = $this->createUpgradeManager(function () use (&$version) {
				$version++;
			});
			self::assertEquals(0, $version);
			self::assertInstanceOf(IUpgrade::class, $upgrade = $upgradeManager->upgrade());
			self::assertEquals(3, $version);
			self::assertEquals('1.2', $upgrade->getVersion());
		}

		protected function createUpgradeManager(callable $callback) {
			$upgradeManager = new UpgradeManager();
			$upgradeManager->registerUpgrade(new CallbackUpgrade($callback, '1.0'));
			$upgradeManager->registerUpgrade(new CallbackUpgrade($callback, '1.1'));
			$upgradeManager->registerUpgrade(new CallbackUpgrade($callback, '1.2'));
			return $upgradeManager;
		}

		public function testUpgradeTo() {
			$version = 0;
			$upgradeManager = $this->createUpgradeManager(function () use (&$version) {
				$version++;
			});
			self::assertEquals(0, $version);
			self::assertInstanceOf(IUpgrade::class, $upgrade = $upgradeManager->upgradeTo('1.1'));
			self::assertEquals(2, $version);
			self::assertEquals('1.1', $upgrade->getVersion());
		}

		public function testException() {
			$this->expectException(UpgradeException::class);
			$this->expectExceptionMessage('Cannot run upgrade - unknown upgrade version [3.4].');
			$version = 0;
			$upgradeManager = $this->createUpgradeManager(function () use (&$version) {
				$version++;
			});
			self::assertEquals(0, $version);
			self::assertInstanceOf(IUpgrade::class, $upgrade = $upgradeManager->upgradeTo('3.4'));
		}
	}
