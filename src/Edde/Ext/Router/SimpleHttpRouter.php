<?php
	declare(strict_types=1);

	namespace Edde\Ext\Router;

	use Edde\Api\Application\LazyContextTrait;
	use Edde\Api\Http\LazyHttpRequestTrait;
	use Edde\Common\Strings\StringUtils;

	class SimpleHttpRouter extends HttpRouter {
		use LazyHttpRequestTrait;
		use LazyContextTrait;

		/**
		 * @inheritdoc
		 */
		public function createRequest() {
			if ($this->runtime->isConsoleMode()) {
				return null;
			}
			$requestUrl = $this->httpRequest->getRequestUrl();
			if (empty($pathList = $requestUrl->getPathList())) {
				return null;
			}
			if (count($pathList) !== 2) {
				return null;
			}
			list($control, $action) = $pathList;
			$partList = [];
			foreach (explode('.', $control) as $part) {
				$partList[] = StringUtils::toCamelCase($part);
			}
			$name = implode('\\', $partList);
			$parameterList = $requestUrl->getParameterList();
			foreach ($this->context->cascade('\\') as $namespace) {
				if (class_exists($class = sprintf('%s\\%s', $namespace, $name))) {
					$parameterList['action'] = $class . '.' . $action;
					break;
				}
			}
			if (isset($parameterList['action']) === false) {
				return null;
			}
			$requestUrl->setParameterList($parameterList);
			return parent::createRequest();
		}
	}
