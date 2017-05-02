<?php
	declare(strict_types=1);

	namespace Edde\Common\Rest;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\IResponse;
	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Http\IResponse as IHttpResponse;
	use Edde\Api\Http\LazyHostUrlTrait;
	use Edde\Api\Http\LazyHttpRequestTrait;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Api\Rest\IService;
	use Edde\Common\Control\AbstractControl;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Url\Url;
	use Edde\Ext\Application\StringResponse;

	abstract class AbstractService extends AbstractControl implements IService {
		use LazyResponseManagerTrait;
		use LazyHttpResponseTrait;
		use LazyHostUrlTrait;
		use LazyHttpRequestTrait;
		protected static $methodList = [
			'GET',
			'POST',
			'PUT',
			'PATCH',
			'DELETE',
			'HEAD',
		];

		/**
		 * @inheritdoc
		 */
		public function link($generate, ...$parameterList) {
			$requestUrl = $this->httpRequest->getRequestUrl();
			$url = Url::create($this->hostUrl->getAbsoluteUrl());
			$url->setPath($generate);
			$parameterList = array_key_exists(0, $parameterList) && $parameterList[0] === null ? [] : array_merge($requestUrl->getParameterList(), $parameterList);
			unset($parameterList['action']);
			$url->setParameterList($parameterList);
			return $url->getAbsoluteUrl();
		}

		/**
		 * @inheritdoc
		 */
		public function request(IRequest $request): IResponse {
			$methodList = $this->getMethodList();
			if (in_array($method = strtoupper($request->getAction()), self::$methodList, true) === false) {
				$this->httpResponse->header('Allowed', $allowed = implode(', ', array_keys($methodList)));
				return $this->error(IHttpResponse::R400_NOT_ALLOWED, sprintf('The requested method [%s] is not supported; %s.', $method, empty($methodList) ? 'there are no supported methods' : 'available methods are [' . $allowed . ']'));
			}
			if (isset($methodList[$method]) === false) {
				$this->httpResponse->header('Allowed', $allowed = implode(', ', array_keys($methodList)));
				return $this->error(IHttpResponse::R400_NOT_ALLOWED, sprintf('The requested method [%s] is not implemented; %s.', $method, empty($methodList) ? 'there are no available methods' : 'available methods are [' . $allowed . ']'));
			}
			return $this->execute($methodList[$method], $request);
		}

		/**
		 * @inheritdoc
		 */
		public function getMethodList(): array {
			$methodList = [];
			foreach (self::$methodList as $name) {
				if (method_exists($this, $method = ('rest' . StringUtils::firstUpper(strtolower($name))))) {
					$methodList[$name] = $method;
				}
			}
			return $methodList;
		}

		protected function error(int $code, string $message) {
			$this->httpResponse->header('Date', gmdate('D, d M Y H:i:s T'));
			return $this->response(new StringResponse($message, ['text/plain']), $code);
		}

		protected function response(IResponse $response, int $code = null) {
			$code ? $this->httpResponse->setCode($code) : null;
			$this->responseManager->response($response);
			return $response;
		}
	}
