<?php
	declare(strict_types=1);

	namespace Edde\Ext\Router;

	class EddeRouter extends HttpRouter {
		public function createRequest() {
			if ($this->runtime->isConsoleMode() || $this->requestUrl->getPath() !== '/edde.setup') {
				return null;
			}
			$parameterList = $this->requestUrl->getQuery();
			$parameterList['action'] = EddeControl::class . '.setup';
			$this->requestUrl->setQuery($parameterList);
			return parent::createRequest();
		}
	}
