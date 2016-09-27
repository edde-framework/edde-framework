<?php
	namespace Edde\Common\Deffered;

	use Edde\Common\AbstractObject;
	use phpunit\framework\TestCase;

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class UsableObject extends AbstractDeffered {
		public $prepared = false;

		public function takeAction() {
			$this->use();
		}

		protected function prepare() {
			$this->prepared = true;
		}
	}

	/** @noinspection PhpHierarchyChecksInspection */
	class UsableTraitedObject extends AbstractObject {
		use DefferedTrait;

		public $prepared = false;

		public function takeAction() {
			$this->use();
		}

		protected function prepare() {
			$this->prepared = true;
		}
	}

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class UsableTest extends TestCase {
		public function testUsableObject() {
			$object = new UsableObject();
			$onSetupFlag = false;
			self::assertFalse($object->prepared);
			self::assertFalse($object->isUsed());
			$object->onSetup(function () use (&$onSetupFlag) {
				$onSetupFlag = true;
			});
			$object->takeAction();
			self::assertTrue($onSetupFlag);
			self::assertTrue($object->prepared);
			self::assertTrue($object->isUsed());
		}

		public function testUseHook() {
			$object = new UsableObject();
			$onUsedFlag = false;
			$object->onUse(function () use (&$onUsedFlag) {
				$onUsedFlag = true;
			});
			self::assertFalse($onUsedFlag);
			$object->takeAction();
			self::assertTrue($onUsedFlag);
		}

		public function testSetupHook() {
			$object = new UsableObject();
			$onSetupFlag = false;
			$object->onSetup(function () use (&$onSetupFlag) {
				$onSetupFlag = true;
			});
			self::assertFalse($onSetupFlag);
			$object->takeAction();
			self::assertTrue($onSetupFlag);
		}

		public function testSetupOrder() {
			$object = new UsableObject();
			$order = [];
			$object->onSetup(function () use (&$order) {
				$order[] = 'setup';
			});
			$object->onUse(function () use (&$order) {
				$order[] = 'use';
			});
			$object->takeAction();
			self::assertEquals([
				'setup',
				'use',
			], $order);
		}

		public function testAfterSetup() {
			$this->expectException(UsableException::class);
			$this->expectExceptionMessage('Cannot add onSetup callback to already used usable [Edde\Common\Usable\UsableObject].');
			$object = new UsableObject();
			$object->takeAction();
			$object->onSetup(function () {
			});
		}

		public function testAfterUse() {
			$this->expectException(UsableException::class);
			$this->expectExceptionMessage('Cannot add onSetup callback to already used usable [Edde\Common\Usable\UsableObject].');
			$object = new UsableObject();
			$object->takeAction();
			$object->onUse(function () {
			});
		}

		public function testOnLoadedHook() {
			$object = new UsableObject();
			$onLoadedFlag = false;
			$object->onLoaded(function () use (&$onLoadedFlag) {
				$onLoadedFlag = true;
			});
			self::assertFalse($onLoadedFlag);
			$object->takeAction();
			self::assertTrue($onLoadedFlag);
		}

		public function testOnLoadedHookImmediate() {
			$object = new UsableObject();
			$onLoadedFlag = false;
			$object->takeAction();
			$object->onLoaded(function () use (&$onLoadedFlag) {
				$onLoadedFlag = true;
			});
			self::assertTrue($onLoadedFlag);
		}

		public function testUsableTraitedObject() {
			$object = new UsableTraitedObject();
			self::assertFalse($object->prepared);
			$object->takeAction();
			self::assertTrue($object->prepared);
		}
	}
