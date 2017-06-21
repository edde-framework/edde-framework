<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Http\IHostUrl;
	use Edde\Api\Job\LazyJobManagerTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Protocol\Event\LazyEventBusTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\LazyElementStoreTrait;
	use Edde\Api\Protocol\LazyProtocolManagerTrait;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Api\Protocol\Request\IRequestService;
	use Edde\Api\Protocol\Request\LazyRequestServiceTrait;
	use Edde\Api\Store\LazyStoreTrait;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Common\Container\UnknownFactoryException;
	use Edde\Common\Http\HostUrl;
	use Edde\Common\Protocol\Event\Event;
	use Edde\Common\Protocol\Request\MissingResponseException;
	use Edde\Common\Protocol\Request\Request;
	use Edde\Common\Protocol\Request\UnhandledRequestException;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Test\TestCase;
	use Edde\Test\ExecutableService;
	use Edde\Test\TestRequestServiceConfigurator;

	require_once __DIR__ . '/../assets/assets.php';

	class ProtocolServiceTest extends TestCase {
		use LazyContainerTrait;
		use LazyProtocolServiceTrait;
		use LazyRequestServiceTrait;
		use LazyEventBusTrait;
		use LazyConverterManagerTrait;
		use LazyStoreTrait;
		use LazyJobManagerTrait;
		use LazyProtocolManagerTrait;
		use LazyElementStoreTrait;

		public function testEventBusExecute() {
			$count = 0;
			$this->store->drop();
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
			$count = 0;
			$this->store->drop();
			$this->eventBus->listen('some cool event', function (Event $event) use (&$count) {
				$count++;
			});
			$this->eventBus->listen('some cool event', function (Event $event) use (&$count) {
				$count++;
			});
			$this->jobManager->queue($event = new Event('some cool event'));
			$this->assertEquals(0, $count);
			$this->jobManager->execute();
			$this->assertEquals(2, $count, 'EventBus has not been executed!');
		}

		public function testRequestExecuteNoResponse() {
			/** @var $response IElement */
			$response = $this->protocolService->execute(new Request(ExecutableService::class . '::noResponse'));
			self::assertEquals('error', $response->getType());
			self::assertEquals(MissingResponseException::class, $response->getAttribute('exception'));
			self::assertEquals('Internal error; request [Edde\Test\ExecutableService::noResponse] got no answer (response).', $response->getAttribute('message'));
		}

		public function testRequestExecute() {
			/** @var $response IElement */
			self::assertInstanceOf(IElement::class, $response = $this->protocolService->execute(new Request(ExecutableService::class . '::method')));
			self::assertEquals('response', $response->getType());
		}

		public function testRequestQueue() {
			$this->store->drop();
			$this->jobManager->queue(($fooRequest = new Request(ExecutableService::class . '::method'))->data(['foo' => 'bar']));
			$this->jobManager->queue(($barRequest = new Request(ExecutableService::class . '::method'))->data(['foo' => 'foo']));
			$this->jobManager->execute();
			self::assertNotEmpty($responseList = $this->requestService->getResponseList());
			self::assertCount(2, $responseList);
			/** @var $foo IElement */
			/** @var $bar IElement */
			list($foo, $bar) = array_values($responseList);
			self::assertEquals('bar', $foo->getMeta('got-this'));
			self::assertEquals('foo', $bar->getMeta('got-this'));
			$foo = $this->requestService->request($fooRequest);
			$bar = $this->requestService->request($barRequest);
			self::assertEquals('bar', $foo->getMeta('got-this'));
			self::assertEquals('foo', $bar->getMeta('got-this'));
		}

		public function testPacket() {
			$this->store->drop();
			$this->jobManager->queue((new Event('foobar', '123')));
			$this->jobManager->queue((new Event('foobar', '456')));
			$this->jobManager->queue((new Request('do something cool', '789')));
			$this->jobManager->queue((new Event('foobar', '321')));
			$packet = $this->protocolManager->createPacket();
			self::assertEmpty($packet->getElementNode('elements'));
		}

		public function testPacketQueue() {
			$this->store->drop();
			$this->protocolManager->queue((new Event('foobar', '123')));
			$this->protocolManager->queue((new Event('foobar', '456')));
			$this->protocolManager->queue((new Request('do something cool', '789')));
			$this->protocolManager->queue((new Event('foobar', '321')));
			$packet = $this->protocolManager->createPacket();
			$packet->setId('123456');
			$packet->getElementNode('elements')->setId('moo');
			$expect = (object)[
				'packet' => (object)[
					'version'  => '1.1',
					'id'       => '123456',
					'origin'   => 'http://localhost/the-void',
					'elements' => (object)[
						'id'      => 'moo',
						'event'   => [
							(object)[
								'id'    => '123',
								'event' => 'foobar',
							],
							(object)[
								'id'    => '456',
								'event' => 'foobar',
							],
							(object)[
								'id'    => '321',
								'event' => 'foobar',
							],
						],
						'request' => (object)[
							'id'      => '789',
							'request' => 'do something cool',
						],
					],
				],
			];
			self::assertEquals($expect, $this->converterManager->convert($packet, INode::class, [\stdClass::class])->convert()->getContent());
		}

		public function testServiceRequest() {
			$this->store->drop();
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
			$response = $this->protocolService->execute($packet);
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
			], $this->converterManager->convert($response, INode::class, [\stdClass::class])->convert()->getContent());
		}

		public function testAsyncPacket() {
			$this->store->drop();
			/** @var $packet IElement */
			$packet = new Packet('::the-void');
			$packet->setId('the-original-packet');
			$packet->element($request = new Request('there is nobody to handle this'));
			$packet->element($request2 = new Request('testquest'));
			$request->setId('741');
			$request2->setId('852');
			$packet->getElementNode('elements')->setId('foo');
			/** @var $response IElement */
			self::assertInstanceOf(IElement::class, $response = $this->protocolManager->execute($packet->async()));
			self::assertEquals('packet', $response->getType());
			self::assertCount(0, $response->getElementList('elements'));
			self::assertCount(1, $response->getElementList('references'));
			self::assertEquals($response, $response->getReferenceBy($packet->getId()));
			$response->setId('123');
			$response->getElementNode('references')->setId('moo');
			self::assertEquals((object)[
				'packet' => (object)[
					'version'    => '1.1',
					'id'         => '123',
					'origin'     => 'http://localhost/the-void',
					'reference'  => 'the-original-packet',
					'references' => (object)[
						'id'     => 'moo',
						'packet' => (object)[
							'version'  => '1.1',
							'id'       => 'the-original-packet',
							'origin'   => '::the-void',
							'async'    => false,
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
			], $this->converterManager->convert($response, INode::class, [\stdClass::class])->convert()->getContent());
			self::assertTrue($this->elementStore->has($packet->getId()));
			self::assertEmpty(iterator_to_array($this->elementStore->getReferenceListBy($packet->getId())));

			$this->jobManager->execute();

			self::assertCount(1, $referenceList = iterator_to_array($this->elementStore->getReferenceListBy($packet->getId())));
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
							'async'    => false,
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
			], $this->converterManager->convert($response, INode::class, [\stdClass::class])->convert()->getContent());
		}

		public function testKaboom() {
			$this->store->drop();
			$response = $this->protocolService->execute($request = new Request('dfdfsfsdfd/foo'));
			self::assertEquals('error', $response->getType());
			self::assertEquals(-102, $response->getAttribute('code'));
			self::assertEquals('Unknown factory [Dfdfsfsdfd] for dependency [Edde\Ext\Protocol\Request\ClassRequestHandler].', $response->getAttribute('message'));
			self::assertEquals(UnknownFactoryException::class, $response->getAttribute('exception'));
			self::assertEquals($request->getId(), $response->getReference());
			self::assertEquals($response, $this->elementStore->load($response->getId()));
			self::assertEquals($request, $this->elementStore->load($request->getId()));
		}

		public function testAsyncKaboom() {
			$this->store->drop();
			$this->protocolService->execute($request = (new Request('dfdfsfsdfd/foo'))->async());
			self::assertTrue($this->jobManager->hasJob(), 'Async Element has not been added as a job!');
			self::assertEquals($request, $this->elementStore->load($request->getId()));
			self::assertEmpty(iterator_to_array($this->elementStore->getReferenceListBy($request->getId())));
			$this->jobManager->execute();
			/** @var $response IElement */
			list($response) = iterator_to_array($this->elementStore->getReferenceListBy($request->getId()));
			self::assertEquals('error', $response->getType());
			self::assertEquals(-102, $response->getAttribute('code'));
			self::assertEquals('Unknown factory [Dfdfsfsdfd] for dependency [Edde\Ext\Protocol\Request\ClassRequestHandler].', $response->getAttribute('message'));
			self::assertEquals(UnknownFactoryException::class, $response->getAttribute('exception'));
			self::assertEquals($request->getId(), $response->getReference());
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
