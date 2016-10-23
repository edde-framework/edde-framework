<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Http\LazyHeaderListTrait;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Api\Http\LazyRequestUrlTrait;
	use Edde\Module\EddeControl;

	class EddeRouter extends HttpRouter {
		use LazyResponseManagerTrait;
		use LazyHttpResponseTrait;
		use LazyHeaderListTrait;
		use LazyRequestUrlTrait;

		public function createRequest() {
			if ($this->requestUrl->getPath() !== '/edde.setup') {
				return null;
			}
			$parameterList = $this->requestUrl->getQuery();
			$parameterList['action'] = EddeControl::class . '.setup';
			$this->requestUrl->setQuery($parameterList);
			return parent::createRequest();
		}
	}
