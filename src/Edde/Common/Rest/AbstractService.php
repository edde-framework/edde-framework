<?php
	declare(strict_types=1);

	namespace Edde\Common\Rest;

	use Edde\Api\Application\IResponse;
	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Api\Rest\IService;
	use Edde\Common\Control\AbstractControl;
	use Edde\Common\Strings\StringUtils;

	abstract class AbstractService extends AbstractControl implements IService {
		use LazyResponseManagerTrait;
		use LazyHttpResponseTrait;

		const OK = 200;
		const OK_CREATED = 201;

		const ERROR_NOT_FOUND = 404;
		const ERROR_NOT_ALLOWED = 405;

		protected static $methodList = [
			'GET',
			'POST',
			'PUT',
			'PATCH',
			'DELETE',
		];

		public function link($generate, ...$parameterList) {
			if ($generate !== static::class) {
				return null;
			}
			$url = Url::create($this->hostUrl->getAbsoluteUrl());
			$url->setPath($generate);
			$url->setQuery(array_merge($this->requestUrl->getQuery(), $parameterList));
			return $url->getAbsoluteUrl();
		}

		public function execute(string $method, array $parameterList) {
			$methodList = $this->getMethodList();
			if (in_array($method = strtoupper($method), self::$methodList, true) === false) {
				$this->httpResponse->header('Allowed', $allowed = implode(', ', array_keys($methodList)));
				$this->error(self::ERROR_NOT_ALLOWED, sprintf('The requested method [%s] is not supported; %s.', $method, empty($methodList) ? 'there are no supported methods' : 'available methods are [' . $allowed . ']'));
				return null;
			}
			if (isset($methodList[$method]) === false) {
				$this->httpResponse->header('Allowed', $allowed = implode(', ', array_keys($methodList)));
				$this->error(self::ERROR_NOT_ALLOWED, sprintf('The requested method [%s] is not implemented; %s.', $method, empty($methodList) ? 'there are no available methods' : 'available methods are [' . $allowed . ']'));
				return null;
			}
			return parent::execute($methodList[$method], $parameterList);
		}

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
			$this->response($message, 'string', $code, 'string');
		}

		protected function response(IResponse $response, int $code = null) {
			if ($code) {
				$this->httpResponse->setCode($code);
			}
			$this->responseManager->response($response);
			return $this;
		}
	}
