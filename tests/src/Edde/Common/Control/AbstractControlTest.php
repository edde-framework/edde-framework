<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control;

	use Edde\Api\Control\ControlException;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class AbstractControlTest extends TestCase {
		public function testCommon() {
			$control = new \TestControl();
			$controlList = [];
			$control->addControl($controlList[] = $another = new \TestControl());
			$control->addControlList([$controlList[] = new \TestControl()]);
			self::assertFalse($control->isLeaf());
			self::assertTrue($another->isLeaf());
			self::assertEmpty($control->getParent());
			self::assertSame($control, $control->getRoot());
			self::assertSame($control, $another->getRoot());
			self::assertSame($control, $another->getParent());
			self::assertSame($controlList, iterator_to_array($control));
			self::assertEquals('pooboo', $control->handle('someMethod', ['foo' => 'poo',], ['boo']));
		}

		public function testHandleException() {
			$this->expectException(ControlException::class);
			$this->expectExceptionMessage('Missing action parameter [TestControl::someMethod(, ...$boo, ...)].');
			$control = new \TestControl();
			self::assertEquals('pooboo', $control->handle('someMethod', ['foo' => 'poo',], []));
		}

		public function testDummyHandle() {
			$control = new \TestControl();
			self::assertEquals('dumyyyy', $control->handle('dummy', [], []));
		}
	}
