<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Api\Http\IHttpRequest;
	use Edde\Common\Http\CookieList;
	use Edde\Common\Http\HeaderList;
	use Edde\Common\Http\HttpRequest;
	use Edde\Common\Http\PostList;
	use Edde\Common\Url\Url;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

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

		public function testNotMatch() {
			$this->httpRequest->setUrl(Url::create('http://localhost/foo/bar'));
			self::assertEmpty($this->restRouter->route());
		}

		public function testMatchUnknown() {
			$this->httpRequest->setUrl(Url::create('http://localhost/api/'));
			self::assertEmpty($this->restRouter->route());
		}

		public function testMatchBasic() {
			$this->httpRequest->setUrl(Url::create('http://localhost/api/'));
			self::assertEmpty($this->restRouter->route());
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				RestRouter::class,
				IHttpRequest::class => function () {
					return new HttpRequest(new PostList(), new HeaderList(), new CookieList());
				},
			]);
			$this->httpRequest = $container->create(IHttpRequest::class);
			$this->restRouter = $container->create(RestRouter::class, 'TestRouter', '/api/');
		}
	}
