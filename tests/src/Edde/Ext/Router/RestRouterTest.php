<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\IApplication;
	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Router\IRoute;
	use Edde\Api\Router\IRouterService;
	use Edde\Common\Application\Application;
	use Edde\Common\Http\CookieList;
	use Edde\Common\Http\HeaderList;
	use Edde\Common\Http\HttpRequest;
	use Edde\Common\Http\HttpResponse;
	use Edde\Common\Http\PostList;
	use Edde\Common\Router\RouterService;
	use Edde\Common\Url\Url;
	use Edde\Ext\Application\RethrowErrorControl;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;
	use TestRouter\TestService;

	require_once(__DIR__ . '/assets/assets.php');

	class RestRouterTest extends TestCase {
		/**
		 * @var RestRouter
		 */
		protected $restRouter;
		/**
		 * @var HttpRequest
		 */
		protected $httpRequest;
		/**
		 * @var IHttpResponse
		 */
		protected $httpResponse;
		/**
		 * @var IApplication
		 */
		protected $application;

		public function testNotMatch() {
			$this->httpRequest->setUrl(Url::create('http://localhost/foo/bar'));
			self::assertEmpty($this->restRouter->route());
		}

		public function testMatchUnknown() {
			$this->httpRequest->setUrl(Url::create('http://localhost/api/'));
			self::assertEmpty($this->restRouter->route());
		}

		public function testNotAllowed() {
			$this->httpRequest->setUrl(Url::create('http://localhost/api/test-service'));
			$this->httpRequest->setMethod('patch');
			self::assertNotEmpty($route = $this->restRouter->route());
			self::assertEquals(TestService::class, $route->getClass());
			$this->application->run();
			self::assertEquals(405, $this->httpResponse->getCode());
			$headers = $this->httpResponse->getHeaderList()
				->array();
			self::assertArrayHasKey('Date', $headers);
			unset($headers['Date']);
			self::assertEquals([
				'Allowed' => 'GET, DELETE',
				'Content-Type' => 'text/plain',
			], $headers);
			self::assertEquals('The requested method [PATCH] is not supported.', $this->httpResponse->getBody());
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				RestRouter::class,
				IRouterService::class => RouterService::class,
				IRoute::class => function (IRouterService $routerService) {
					return $routerService->route();
				},
				IErrorControl::class => RethrowErrorControl::class,
				IApplication::class => Application::class,
				IHttpRequest::class => function () {
					return new HttpRequest(new PostList(), new HeaderList(), new CookieList());
				},
				IHttpResponse::class => HttpResponse::class,
			]);
			$routerService = $container->create(IRouterService::class);
			$routerService->registerRouter($this->restRouter = $container->create(RestRouter::class));
			$this->restRouter->registerService($container->create(TestService::class));
			$this->httpRequest = $container->create(IHttpRequest::class);
			$this->httpResponse = $container->create(IHttpResponse::class);
			$this->application = $container->create(IApplication::class);
		}
	}
