<?php
	declare(strict_types = 1);

	namespace Edde\Common\Rest;

	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Rest\IService;
	use Edde\Common\Application\Response;
	use Edde\Common\Control\AbstractControl;
	use Edde\Common\Strings\StringUtils;

	abstract class AbstractService extends AbstractControl implements IService {
		const OK_CREATED = 201;

		const ERROR_NOT_FOUND = 404;
		const ERROR_NOT_ALOWED = 405;

		protected static $methodList = [
			'GET',
			'POST',
			'PUT',
			'PATCH',
			'DELETE',
		];

		/**
		 * @var IHttpResponse
		 */
		protected $httpResponse;
		/**
		 * @var IResponseManager
		 */
		protected $responseManager;

		public function lazyHttpResponse(IHttpResponse $httpResponse) {
			$this->httpResponse = $httpResponse;
		}

		public function lazyResponseManager(IResponseManager $responseManager) {
			$this->responseManager = $responseManager;
		}

		public function execute(string $method, array $parameterList) {
			$methodList = $this->getMethodList();
			if (in_array($method = strtoupper($method), self::$methodList, true) === false) {
				$headerList = $this->httpResponse->getHeaderList();
				$headerList->set('Allowed', $allowed = implode(', ', array_keys($methodList)));
				$this->error(self::ERROR_NOT_ALOWED, sprintf('The requested method [%s] is not supported; supported methods are [%s].', $method, $allowed));
				return null;
			}
			if (isset($methodList[$method]) === false) {
				$headerList = $this->httpResponse->getHeaderList();
				$headerList->set('Allowed', $allowed = implode(', ', array_keys($methodList)));
				$this->error(self::ERROR_NOT_ALOWED, sprintf('The requested method [%s] is not implemented; available methods are [%s].', $method, $allowed));
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
			$headerList = $this->httpResponse->getHeaderList();
			$headerList->set('Date', gmdate('D, d M Y H:i:s T'));
			$this->response('text/plain', $message, $code);
		}

		protected function response(string $target, $response, int $code = null, string $contentType = null) {
			if ($code) {
				$this->httpResponse->setCode($code);
			}
			if ($contentType) {
				$this->httpResponse->contentType($contentType);
			}
			$this->responseManager->response(new Response('http+' . $target, $response));
			return $this;
		}
	}
