<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Http\IHostUrl;
	use Edde\Api\Node\INode;
	use Edde\Api\Protocol\Event\LazyEventBusTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Api\Protocol\Request\IRequestService;
	use Edde\Api\Protocol\Request\LazyRequestServiceTrait;
	use Edde\Api\Protocol\Request\UnhandledRequestException;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Common\Container\LazyTrait;
	use Edde\Common\Http\HostUrl;
	use Edde\Common\Protocol\Event\Event;
	use Edde\Common\Protocol\Request\MissingResponseException;
	use Edde\Common\Protocol\Request\Request;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Test\ExecutableService;
	use Edde\Test\TestRequestServiceConfigurator;
	use PHPUnit\Framework\TestCase;

	require_once __DIR__ . '/../assets/assets.php';

	class ProtocolServiceTest extends TestCase implements ILazyInject {
		use LazyContainerTrait;
		use LazyProtocolServiceTrait;
		use LazyRequestServiceTrait;
		use LazyEventBusTrait;
		use LazyConverterManagerTrait;
		use LazyTrait;

		public function testEventBusExecute() {
			$count = 0;
			$this->eventBus->listen('some cool event', function (Event $event) use (&$count) {
				$count++;
			});
			$this->eventBus->listen('some cool event', function (Event $event) use (&$count) {
				$count++;
			});
			$this->protocolService->element($event = new Event('some cool event'));
			$this->assertEquals(2, $count);
			$this->assertNotEmpty($id = $event->getId());
			$this->assertEquals($id, $event->getId());
		}

		public function testEventBusQueue() {
			$count = 0;
			$this->eventBus->listen('some cool event', function (Event $event) use (&$count) {
				$count++;
			});
			$this->eventBus->listen('some cool event', function (Event $event) use (&$count) {
				$count++;
			});
			$this->protocolService->queue($event = new Event('some cool event'));
			$this->assertEquals(0, $count);
			$this->protocolService->dequeue();
			$this->assertEquals(2, $count, 'EventBus has not been executed!');
		}

		public function testRequestExecuteError() {
			/** @var $response INode */
			self::assertInstanceOf(INode::class, $response = $this->requestService->element(new Request('wanna do something')));
			self::assertEquals('error', $response->getName());
			self::assertEquals(UnhandledRequestException::class, $response->getAttribute('exception'));
			self::assertEquals('Unhandled request [wanna do something].', $response->getAttribute('message'));
		}

		public function testRequestExecuteNoResponse() {
			$this->expectException(MissingResponseException::class);
			$this->expectExceptionMessage('Missing response for request [' . ExecutableService::class . '::noResponse].');
			$this->protocolService->element(new Request(ExecutableService::class . '::noResponse'));
		}

		public function testRequestExecute() {
			/** @var $response IElement */
			self::assertInstanceOf(IElement::class, $response = $this->protocolService->element(new Request(ExecutableService::class . '::method')));
			self::assertEquals('response', $response->getType());
		}

		public function testRequestQueue() {
			$this->protocolService->queue(($fooRequest = new Request(ExecutableService::class . '::method'))->data(['foo' => 'bar']));
			$this->protocolService->queue(($barRequest = new Request(ExecutableService::class . '::method'))->data(['foo' => 'foo']));
			$this->protocolService->dequeue();
			self::assertNotEmpty($responseList = $this->requestService->getResponseList());
			self::assertCount(2, $responseList);
			/** @var $foo IElement */
			/** @var $bar IElement */
			list($foo, $bar) = array_values($responseList);
			self::assertEquals('bar', $foo->getMeta('data'));
			self::assertEquals('foo', $bar->getMeta('data'));
			$foo = $this->requestService->request($fooRequest);
			$bar = $this->requestService->request($barRequest);
			self::assertEquals('bar', $foo->getMeta('data'));
			self::assertEquals('foo', $bar->getMeta('data'));
		}

		public function testScopeAndTagList() {
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
			self::assertTrue($event->hasTagList([]));
			self::assertFalse($event->hasTagList(['moooo']));
			self::assertTrue($event->hasTagList([
				'foo',
				'bar',
			]));
			self::assertTrue($event2->hasTagList([
				'foo',
				'bar',
			]));
			self::assertFalse($event2->hasTagList([
				'foo',
				'bar',
				'muhaa',
			]));
			self::assertFalse($event2->hasTagList([
				'muhaa',
				'moooo',
			]));
			self::assertTrue($event2->hasTagList([
				'bar',
			]));
			self::assertTrue($event2->hasTagList([
				'foo',
				'bar',
			]));
		}

		public function testPacket() {
			$this->protocolService->queue((new Event('foobar', '123'))->setScope('scope')->setTagList([
				'foo',
				'bar',
			]));
			$this->protocolService->queue((new Event('foobar', '456'))->setScope('scope')->setTagList([
				'foo',
				'bar',
				'moo',
			]));
			$this->protocolService->queue((new Request('do something cool', '789'))->setScope('scope')->setTagList([
				'foo',
				'bar',
			]));
			$this->protocolService->queue((new Event('foobar', '321'))->setScope('out of scope')->setTagList([
				'foo',
				'bar',
				'moo',
			]));
			$packet = $this->protocolService->createQueuePacket('scope', [
				'foo',
				'bar',
			]);
			$packet->setId('123456');
			$packet->getElementNode('elements')->setId('moo');
			$expect = (object)[
				'packet' => (object)[
					'version'  => '1.1',
					'id'       => '123456',
					'origin'   => 'http://localhost/the-void',
					'scope'    => 'scope',
					'tags'     => [
						'foo',
						'bar',
					],
					'elements' => (object)[
						'id'      => 'moo',
						'event'   => [
							(object)[
								'id'    => '123',
								'scope' => 'scope',
								'tags'  => [
									'foo',
									'bar',
								],
								'event' => 'foobar',
							],
							(object)[
								'id'    => '456',
								'scope' => 'scope',
								'tags'  => [
									'foo',
									'bar',
									'moo',
								],
								'event' => 'foobar',
							],
						],
						'request' => (object)[
							'id'      => '789',
							'scope'   => 'scope',
							'tags'    => [
								'foo',
								'bar',
							],
							'request' => 'do something cool',
						],
					],
				],
			];
			self::assertEquals($expect, $this->converterManager->convert($packet, INode::class, [\stdClass::class])->convert());
		}

		public function testServiceRequest() {
			$packet = new Packet('::the-void');
			$packet->setId('321');
			$request = new Request('there is nobody to handle this');
			$packet->element($request);
			$request2 = new Request('testquest');
			$packet->element($request2);
			$request->setId('852');
			$request2->setId('963');
			$packet->getElementNode('elements')->setId('foo');
			self::assertEquals('packet', $packet->getType());
			/** @var $response IElement */
			$response = $this->protocolService->element($packet);
			self::assertNotEquals($packet->getId(), $response->getId());
			self::assertNotEquals($packet, $response);
			self::assertCount(2, $response->getElementList('elements'));
			self::assertCount(3, $response->getElementList('references'));
			self::assertEquals($response, $response->getReferenceBy($packet->getId()));

			self::assertInstanceOf(IElement::class, $element = $response->getReferenceBy($request->getId()));
			self::assertSame('error', $element->getType());
			self::assertSame(UnhandledRequestException::class, $element->getAttribute('exception'));
			$element->setId('456');

			self::assertInstanceOf(IElement::class, $element = $response->getReferenceBy($request2->getId()));
			self::assertEquals(['a' => 'b'], $element->getMetaList()->array());

			$response->setId('123');
			$response->getElementNode('elements')->setId('moo');
			$response->getElementNode('references')->setId('foomoo');
			self::assertEquals((object)[
				'packet' => (object)[
					'version'    => '1.1',
					'id'         => '123',
					'origin'     => 'http://localhost/the-void',
					'elements'   => (object)[
						'id'       => 'moo',
						'error'    => (object)[
							'id'        => '456',
							'code'      => 100,
							'message'   => 'Unhandled request [there is nobody to handle this].',
							'reference' => '852',
							'exception' => UnhandledRequestException::class,
						],
						'response' => (object)[
							'id'        => 'foobar',
							'reference' => '963',
							'::meta'    => ['a' => 'b'],
						],
					],
					'reference'  => '321',
					'references' => (object)[
						'id'      => 'foomoo',
						'packet'  => (object)[
							'version'  => '1.1',
							'id'       => '321',
							'origin'   => '::the-void',
							'elements' => (object)[
								'id'      => 'foo',
								'request' => [
									(object)[
										'id'      => '852',
										'request' => 'there is nobody to handle this',
									],
									(object)[
										'id'      => '963',
										'request' => 'testquest',
									],
								],
							],
						],
						'request' => [
							(object)[
								'id'      => '852',
								'request' => 'there is nobody to handle this',
							],
							(object)[
								'id'      => '963',
								'request' => 'testquest',
							],
						],
					],
				],
			], $this->converterManager->convert($response, INode::class, [\stdClass::class])->convert());
		}

		public function testAsyncPacket() {
			$packet = new Packet('::the-void');
			$packet->setId('the-original-packet');
			$packet->element($request = new Request('there is nobody to handle this'));
			$packet->element($request2 = new Request('testquest'));
			$request->setId('741');
			$request2->setId('852');
			$packet->getElementNode('elements')->setId('foo');
			/** @var $response IElement */
			self::assertInstanceOf(IElement::class, $response = $this->protocolService->element($packet->async()));
			self::assertEquals('packet', $response->getType());
			self::assertCount(0, $response->getElementList('elements'));
			self::assertCount(1, $response->getElementList('references'));
			self::assertEquals($response, $response->getReferenceBy($packet->getId()));
			$response->setId('123');
			$response->getElementNode('references')->setId('moo');
			self::assertEquals((object)[
				'packet' => (object)[
					'version' => '1.1',

					'id'         => '123',
					'origin'     => 'http://localhost/the-void',
					'reference'  => 'the-original-packet',
					'references' => (object)[
						'id'     => 'moo',
						'packet' => (object)[
							'version'  => '1.1',
							'id'       => 'the-original-packet',
							'origin'   => '::the-void',
							'async'    => true,
							'elements' => (object)[
								'id'      => 'foo',
								'request' => [
									(object)[
										'id'      => '741',
										'request' => 'there is nobody to handle this',
									],
									(object)[
										'id'      => '852',
										'request' => 'testquest',
									],
								],
							],
						],
					],
				],
			], $this->converterManager->convert($response, INode::class, [\stdClass::class])->convert());
			self::assertEmpty($this->protocolService->getReferenceList($packet->getId()));

			$this->protocolService->dequeue();

			self::assertCount(1, $referenceList = $this->protocolService->getReferenceList($packet->getId()));
			list($response) = $referenceList;
			self::assertInstanceOf(IElement::class, $response);
			self::assertEquals('packet', $response->getType());

			self::assertCount(2, $response->getElementList('elements'));
			self::assertCount(3, $response->getElementList('references'));
			self::assertEquals($response, $response->getReferenceBy($packet->getId()));

			self::assertInstanceOf(IElement::class, $element = $response->getReferenceBy($request->getId()));
			self::assertEquals('error', $element->getType());
			self::assertEquals(UnhandledRequestException::class, $element->getAttribute('exception'));
			$element->setId('456');

			self::assertInstanceOf(IElement::class, $element = $response->getReferenceBy($request2->getId()));
			self::assertEquals('response', $element->getType());
			self::assertEquals(['a' => 'b'], $element->getData());
			$response->setId('123');
			$response->getElementNode('elements')->setId('foo');
			$response->getElementNode('references')->setId('moo');
			self::assertEquals((object)[
				'packet' => (object)[
					'version'    => '1.1',
					'id'         => '123',
					'origin'     => 'http://localhost/the-void',
					'elements'   => (object)[
						'id'       => 'foo',
						'error'    => (object)[
							'id'        => '456',
							'code'      => 100,
							'message'   => 'Unhandled request [there is nobody to handle this].',
							'reference' => '741',
							'exception' => UnhandledRequestException::class,
						],
						'response' => (object)[
							'id'        => 'foobar',
							'reference' => '852',
							'::meta'    => ['a' => 'b'],
						],
					],
					'reference'  => 'the-original-packet',
					'references' => (object)[
						'id'      => 'moo',
						'packet'  => (object)[
							'version'  => '1.1',
							'id'       => 'the-original-packet',
							'origin'   => '::the-void',
							'async'    => true,
							'elements' => (object)[
								'id'      => 'foo',
								'request' => [
									(object)[
										'id'      => '741',
										'request' => 'there is nobody to handle this',
									],
									(object)[
										'id'      => '852',
										'request' => 'testquest',
									],
								],
							],
						],
						'request' => [
							(object)[
								'id'      => '741',
								'request' => 'there is nobody to handle this',
							],
							(object)[
								'id'      => '852',
								'request' => 'testquest',
							],
						],
					],
				],
			], $this->converterManager->convert($response, INode::class, [\stdClass::class])->convert());
		}

		protected function setUp() {
			ContainerFactory::autowire($this, [
				IHostUrl::class => ContainerFactory::instance(HostUrl::class, ['http://localhost/the-void']),
				new ClassFactory(),
			], [
				IRequestService::class => TestRequestServiceConfigurator::class,
			]);
		}
	}
