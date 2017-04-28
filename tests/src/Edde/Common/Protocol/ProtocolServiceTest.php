<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Protocol\Event\LazyEventBusTrait;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Api\Protocol\Request\IResponse;
	use Edde\Api\Protocol\Request\UnhandledRequestException;
	use Edde\Common\Container\LazyTrait;
	use Edde\Common\Protocol\Event\Event;
	use Edde\Common\Protocol\Request\MissingResponseException;
	use Edde\Common\Protocol\Request\Request;
	use Edde\Ext\Container\ClassFactory;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Test\ExecutableService;
	use PHPUnit\Framework\TestCase;

	require_once __DIR__ . '/../assets/assets.php';

	class ProtocolServiceTest extends TestCase implements ILazyInject {
		use LazyProtocolServiceTrait;
		use LazyEventBusTrait;
		use LazyTrait;

		public function testEventException() {
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

		public function testRequestException() {
			$this->expectException(NoHandlerException::class);
			$this->expectExceptionMessage('Element [request (' . Request::class . ')] has no available handler.');
			$this->protocolService->execute(new Request('wanna do something'));
		}

		public function testRequestExecuteException() {
			$this->expectException(UnhandledRequestException::class);
			$this->expectExceptionMessage('Unhandled request [wanna do something (' . Request::class . ')].');
			$this->protocolService->setup();
			$this->protocolService->execute(new Request('wanna do something'));
		}

		public function testRequestExecuteNoResponse() {
			$this->expectException(MissingResponseException::class);
			$this->expectExceptionMessage('Missing response for request [' . ExecutableService::class . '::noResponse].');
			$this->protocolService->setup();
			$this->protocolService->execute(new Request(ExecutableService::class . '::noResponse'));
		}

		public function testRequestExecute() {
			$this->protocolService->setup();
			self::assertNotEmpty($response = $this->protocolService->execute(new Request(ExecutableService::class . '::method')));
			self::assertInstanceOf(IResponse::class, $response);
		}

		protected function setUp() {
			ContainerFactory::autowire($this, [new ClassFactory()]);
		}
	}
