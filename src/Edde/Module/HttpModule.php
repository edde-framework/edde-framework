<?php
	declare(strict_types = 1);

	namespace Edde\Module;

	use Edde\Api\Client\IHttpClient;
	use Edde\Api\Http\IBody;
	use Edde\Api\Http\ICookieList;
	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Http\IHttpRequestFactory;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Http\IPostList;
	use Edde\Api\Http\IRequestUrl;
	use Edde\Common\Client\HttpClient;
	use Edde\Common\Http\HttpRequestFactory;
	use Edde\Common\Http\HttpResponse;
	use Edde\Common\Runtime\AbstractModule;
	use Edde\Common\Runtime\Event\SetupEvent;

	class HttpModule extends AbstractModule {
		public function setupHttpModule(SetupEvent $setupEvent) {
			$runtime = $setupEvent->getRuntime();
			$runtime->registerFactoryList([
				IHttpRequestFactory::class => HttpRequestFactory::class,
				IHttpRequest::class => function (IHttpRequestFactory $httpRequestFactory) {
					return $httpRequestFactory->create();
				},
				IRequestUrl::class => function (IHttpRequest $httpRequest) {
					return $httpRequest->getRequestUrl();
				},
				IHeaderList::class => function (IHttpRequest $httpRequest) {
					return $httpRequest->getHeaderList();
				},
				ICookieList::class => function (IHttpRequest $httpRequest) {
					return $httpRequest->getCookieList();
				},
				IPostList::class => function (IHttpRequest $httpRequest) {
					return $httpRequest->getPostList();
				},
				IBody::class => function (IHttpRequest $httpRequest) {
					return $httpRequest->getBody();
				},
				IHttpResponse::class => HttpResponse::class,
				IHttpClient::class => HttpClient::class,
			]);
		}
	}
