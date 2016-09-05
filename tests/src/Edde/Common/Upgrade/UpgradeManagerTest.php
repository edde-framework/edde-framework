<?php
	declare(strict_types = 1);

	namespace Edde\Common\Upgrade;

	use Edde\Api\Storage\IStorage;
	use Edde\Api\Upgrade\IUpgrade;
	use Edde\Api\Upgrade\IUpgradeManager;
	use Edde\Api\Upgrade\UpgradeException;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class UpgradeManagerTest extends TestCase {
		/**
		 * @var IUpgradeManager
		 */
		protected $upgradeManager;

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
			$this->upgradeManager->registerUpgrade(new CallbackUpgrade($callback, '1.0'));
			$this->upgradeManager->registerUpgrade(new CallbackUpgrade($callback, '1.1'));
			$this->upgradeManager->registerUpgrade(new CallbackUpgrade($callback, '1.2'));
			return $this->upgradeManager;
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

		protected function setUp() {
			$container = ContainerFactory::create([
				IStorage::class => \DummyStorage::class,
				IUpgradeManager::class => UpgradeManager::class,
			]);
			$this->upgradeManager = $container->create(IUpgradeManager::class);
		}
	}
