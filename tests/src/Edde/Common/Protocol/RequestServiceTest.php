<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\Request\LazyRequestServiceTrait;
	use Edde\Api\Protocol\Request\UnhandledRequestException;
	use Edde\Common\Protocol\Event\Event;
	use Edde\Common\Protocol\Request\MissingResponseException;
	use Edde\Common\Protocol\Request\Request;
	use Edde\Ext\Test\TestCase;
	use Edde\Test\ExecutableService;

	require_once __DIR__ . '/../assets/assets.php';

	class RequestServiceTest extends TestCase {
		use LazyRequestServiceTrait;

		public function testUnknownElement() {
			$this->expectException(UnsupportedElementException::class);
			$this->expectExceptionMessage('Unsupported element [event] in protocol handler [Edde\Common\Protocol\Request\RequestService].');
			$this->requestService->element(new Event('foo'));
		}

		public function testMissingResponse() {
			/** @var $response IElement */
			$response = $this->requestService->element(new Request(sprintf('%s::noResponse', ExecutableService::class)));
			self::assertEquals('error', $response->getType());
			self::assertEquals(MissingResponseException::class, $response->getAttribute('exception'));
			self::assertEquals('Internal error; request [Edde\Test\ExecutableService::noResponse] got no answer (response).', $response->getAttribute('message'));
		}

		public function testUnhandlerRequest() {
			/** @var $response IElement */
			$response = $this->requestService->element(new Request('unhandled'));
			self::assertEquals('error', $response->getType());
			self::assertEquals(UnhandledRequestException::class, $response->getAttribute('exception'));
			self::assertEquals('Unhandled request [unhandled].', $response->getAttribute('message'));
		}

		public function testContainerHandler() {
			/** @var $response IElement */
			$response = $this->requestService->element((new Request(sprintf('%s::method', ExecutableService::class)))->data(['foo' => 'bababar']));
			self::assertEquals('response', $response->getType());
			self::assertEquals('bababar', $response->getMeta('got-this'));
		}
	}
