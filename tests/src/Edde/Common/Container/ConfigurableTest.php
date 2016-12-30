<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\IConfigurable;
	use PHPUnit\Framework\TestCase;

	require_once __DIR__ . '/assets/assets.php';

	class ConfigurableTest extends TestCase {
		/**
		 * @var IConfigurable
		 */
		protected $configurable;

		public function testConfigurableInit() {
			$object = new \AnotherSomething();
			self::assertFalse($object->isInitialized());
			self::assertFalse($object->isConfigured());
			self::assertFalse($object->isWarmedup());
			self::assertFalse($object->isSetup());
			$object->init();
			self::assertTrue($object->isInitialized());
			self::assertFalse($object->isWarmedup());
			self::assertFalse($object->isConfigured());
			self::assertFalse($object->isSetup());
		}

		public function testConfigurableWarmup() {
			$object = new \AnotherSomething();
			self::assertFalse($object->isInitialized());
			self::assertFalse($object->isConfigured());
			self::assertFalse($object->isWarmedup());
			self::assertFalse($object->isSetup());
			$object->warmup();
			self::assertTrue($object->isInitialized());
			self::assertTrue($object->isWarmedup());
			self::assertFalse($object->isConfigured());
			self::assertFalse($object->isSetup());
		}

		public function testConfigurableConfig() {
			$object = new \AnotherSomething();
			self::assertFalse($object->isInitialized());
			self::assertFalse($object->isConfigured());
			self::assertFalse($object->isWarmedup());
			self::assertFalse($object->isSetup());
			$object->config();
			self::assertTrue($object->isInitialized());
			self::assertTrue($object->isWarmedup());
			self::assertTrue($object->isConfigured());
			self::assertFalse($object->isSetup());
		}

		public function testConfigurableSetup() {
			$object = new \AnotherSomething();
			self::assertFalse($object->isInitialized());
			self::assertFalse($object->isWarmedup());
			self::assertFalse($object->isConfigured());
			self::assertFalse($object->isSetup());
			$object->setup();
			self::assertTrue($object->isInitialized());
			self::assertTrue($object->isWarmedup());
			self::assertTrue($object->isConfigured());
			self::assertTrue($object->isSetup());
		}

		protected function setUp() {
			$this->configurable = new \AnotherSomething();
		}
	}
