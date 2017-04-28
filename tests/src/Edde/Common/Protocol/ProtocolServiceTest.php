<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Protocol\Event\LazyEventBusTrait;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Common\Container\LazyTrait;
	use Edde\Common\Protocol\Event\Event;
	use Edde\Ext\Container\ClassFactory;
	use Edde\Ext\Container\ContainerFactory;
	use PHPUnit\Framework\TestCase;

	class ProtocolServiceTest extends TestCase implements ILazyInject {
		use LazyProtocolServiceTrait;
		use LazyEventBusTrait;
		use LazyTrait;

		public function testException() {
			$this->expectException(NoHandlerException::class);
			$this->expectExceptionMessage('Element [event (' . Event::class . ')] has no available handler.');
			$this->protocolService->execute(new Event('some cool event'));
		}

		public function testEventBusExecute() {
			$this->protocolService->setup();
			$count = 0;
			$this->eventBus->listen('some cool event', function (Event $event) use (&$count) {
				$count++;
			});
			$this->eventBus->listen('some cool event', function (Event $event) use (&$count) {
				$count++;
			});
			$this->protocolService->execute($event = new Event('some cool event'));
			$this->assertEquals(2, $count);
			$this->assertNotEmpty($id = $event->getId());
			$this->assertEquals($id, $event->getId());
		}

		public function testEventBusQueue() {
			$this->protocolService->setup();
			$count = 0;
			$this->eventBus->listen('some cool event', function (Event $event) use (&$count) {
				$count++;
			});
			$this->eventBus->listen('some cool event', function (Event $event) use (&$count) {
				$count++;
			});
			$this->protocolService->queue(new Event('some cool event'));
			$this->assertEquals(0, $count);
			$this->protocolService->dequeue();
			$this->assertEquals(2, $count, 'EventBus has not been executed!');
		}

		protected function setUp() {
			ContainerFactory::autowire($this, [new ClassFactory()]);
		}
	}
