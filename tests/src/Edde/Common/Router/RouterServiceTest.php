<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Router\Inject\RouterService;
	use Edde\Ext\Test\TestCase;
	use Edde\Test\TestRouter;

	require_once __DIR__ . '/../assets/assets.php';

	class RouterServiceTest extends TestCase {
		use RouterService;

		public function testCanHandle() {
			self::assertTrue($this->routerService->canHandle());
		}

		public function testCreateRequest() {
			$request = $this->routerService->createRequest();
			$element = $request->getElement();
			self::assertSame('message', $element->getType());
			self::assertSame('foo.foo-service/foo-action', $element->getAttribute('request'));
		}

		protected function setUp() {
			parent::setUp();
			$this->routerService->registerRouter(new TestRouter());
		}

	}
