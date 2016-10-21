<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Http\LazyHeaderListTrait;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Api\Http\LazyRequestUrlTrait;
	use Edde\Common\Application\Request;
	use Edde\Common\Router\AbstractRouter;
	use Edde\Module\EddeControl;

	class EddeRouter extends AbstractRouter {
		use LazyResponseManagerTrait;
		use LazyHttpResponseTrait;
		use LazyHeaderListTrait;
		use LazyRequestUrlTrait;

		public function createRequest() {
			if ($this->requestUrl->getPath() !== '/edde.setup') {
				return null;
			}
			$this->httpResponse->setContentType($mime = $this->headerList->getContentType()
				->getMime($this->headerList->getAccept()));
			$this->responseManager->setMime($mime = ('http+' . $mime));
			return new Request($mime, EddeControl::class, 'actionSetup', []);
		}
	}
