<?php
	declare(strict_types=1);

	namespace Edde\Common\Request;

	use Edde\Api\Request\Inject\RequestService;
	use Edde\Ext\Test\TestCase;

	class RequestServiceTest extends TestCase {
		use RequestService;

		public function testCanHandle() {
			self::assertTrue($this->requestService->canHandle(new Message('foo.foo-service/foo-action')));
		}
	}
