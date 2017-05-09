<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Protocol\Event\LazyEventBusTrait;
	use Edde\Api\Protocol\IError;
	use Edde\Api\Protocol\IPacket;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Api\Protocol\Request\IResponse;
	use Edde\Api\Protocol\Request\LazyRequestServiceTrait;
	use Edde\Api\Protocol\Request\UnhandledRequestException;
	use Edde\Common\Container\LazyTrait;
	use Edde\Common\Protocol\Event\Event;
	use Edde\Common\Protocol\Request\MissingResponseException;
	use Edde\Common\Protocol\Request\Request;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Test\ExecutableService;
	use PHPUnit\Framework\TestCase;

	require_once __DIR__ . '/../assets/assets.php';

	class ProtocolServiceTest extends TestCase implements ILazyInject {
		use LazyContainerTrait;
		use LazyProtocolServiceTrait;
		use LazyRequestServiceTrait;
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

		public function testRequestExecuteError() {
			$this->requestService->setup();
			self::assertInstanceOf(IError::class, $response = $this->requestService->execute($request = new Request('wanna do something')));
			/** @var $response IError */
			self::assertEquals('error', $response->getType());
			self::assertEquals(UnhandledRequestException::class, $response->getException());
			self::assertEquals('Unhandled request [wanna do something (Edde\Common\Protocol\Request\Request)].', $response->getMessage());
			$this->requestService->request($request);
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

		public function testRequestQueue() {
			$this->protocolService->setup();
			$this->protocolService->queue(($fooRequest = new Request(ExecutableService::class . '::method'))->put(['foo' => 'bar']));
			$this->protocolService->queue(($barRequest = new Request(ExecutableService::class . '::method'))->put(['foo' => 'foo']));
			$this->protocolService->dequeue();
			self::assertNotEmpty($responseList = $this->requestService->getResponseList());
			self::assertCount(2, $responseList);
			/** @var $foo IResponse */
			/** @var $bar IResponse */
			list($foo, $bar) = array_values($responseList);
			self::assertEquals('bar', $foo->get('data'));
			self::assertEquals('foo', $bar->get('data'));
			$foo = $this->requestService->request($fooRequest);
			$bar = $this->requestService->request($barRequest);
			self::assertEquals('bar', $foo->get('data'));
			self::assertEquals('foo', $bar->get('data'));
		}

		public function testAligment() {
			$event = (new Event('foobar'))->setScope('scope')->setTagList([
				'foo',
				'bar',
			]);
			$event2 = (new Event('foobar'))->setScope('scope')->setTagList([
				'foo',
				'bar',
				'moo',
			]);
			self::assertTrue($event->inScope('scope'));
			self::assertFalse($event->inScope('scopee'));
			self::assertFalse($event->inTagList());
			self::assertTrue($event->inTagList([
				'foo',
				'bar',
			]));
			self::assertTrue($event->inTagList([
				'foo',
				'bar',
			], true));
			self::assertTrue($event2->inTagList([
				'foo',
				'bar',
			]));
			self::assertTrue($event2->inTagList([
				'foo',
				'bar',
				'muhaa',
			]));
			self::assertFalse($event2->inTagList([
				'muhaa',
				'moooo',
			]));
			self::assertTrue($event2->inTagList([
				'bar',
			]));
			self::assertFalse($event2->inTagList([
				'foo',
				'bar',
			], true));
		}

		public function testPacket() {
			$this->protocolService->setup();
			$this->protocolService->queue(($event = new Event('foobar'))->setScope('scope')->setTagList([
				'foo',
				'bar',
			]));
			$this->protocolService->queue(($event2 = new Event('foobar'))->setScope('scope')->setTagList([
				'foo',
				'bar',
				'moo',
			]));
			$this->protocolService->queue(($request = new Request('do something cool'))->setScope('scope')->setTagList([
				'foo',
				'bar',
			]));
			$this->protocolService->queue(($event3 = new Event('foobar'))->setScope('out of scope')->setTagList([
				'foo',
				'bar',
				'moo',
			]));
			$packet = $this->protocolService->packet('scope', [
				'foo',
				'bar',
			]);
			$packet = $packet->packet();
			$expect = new \stdClass();
			$expect->version = '1.0';
			$expect->type = 'packet';
			$expect->scope = 'scope';
			$expect->tags = [
				'foo',
				'bar',
			];
			$expect->elements = [
				(object)[
					'type'  => 'event',
					'scope' => 'scope',
					'tags'  => [
						'foo',
						'bar',
					],
					'event' => 'foobar',
				],
				(object)[
					'type'  => 'event',
					'scope' => 'scope',
					'tags'  => [
						'foo',
						'bar',
						'moo',
					],
					'event' => 'foobar',
				],
				(object)[
					'type'    => 'request',
					'scope'   => 'scope',
					'tags'    => [
						'foo',
						'bar',
					],
					'request' => 'do something cool',
				],
			];
			self::assertEquals($expect, $packet);
		}

		public function testServiceRequest() {
			$this->protocolService->setup();

			/** @var $packet IPacket */
			$packet = $this->container->create(IPacket::class);
			$packet->addElement($request = new Request('there is nobody to handle this'));

			self::assertEquals('packet', $packet->getType());
			$response = $this->protocolService->request($packet);
			self::assertNotEquals($packet->getId(), $response->getId());
			self::assertNotEquals($packet, $response);
			self::assertCount(1, $response->getElementList());
			self::assertCount(2, $response->getReferenceList());
			self::assertEquals($response, $response->reference($packet));

			/** @var $element IError */
			self::assertInstanceOf(IError::class, $element = $response->reference($request));
			self::assertSame(UnhandledRequestException::class, $element->getType());
		}

		protected function setUp() {
			ContainerFactory::autowire($this);
		}
	}
