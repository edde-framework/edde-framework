<?php
	declare(strict_types = 1);

	namespace Edde\Common\Rest;

	use Edde\Api\Http\IHttpResponse;
	use Edde\Api\Rest\IService;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Control\AbstractControl;
	use Edde\Common\Response\TextResponse;
	use Edde\Common\Strings\StringUtils;

	abstract class AbstractService extends AbstractControl implements IService {
		use LazyInjectTrait;

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

		public function lazyHttpResponse(IHttpResponse $httpResponse) {
			$this->httpResponse = $httpResponse;
		}

		public function execute(string $method, array $parameterList) {
			$methodList = $this->getMethodList();
			if (in_array($method = strtoupper($method), self::$methodList, true) === false) {
				$this->httpResponse->setCode(self::ERROR_NOT_ALOWED);
				$headerList = $this->httpResponse->getHeaderList();
				$headerList->set('Allowed', $allowed = implode(', ', array_keys($methodList)));
				$headerList->set('Date', gmdate('D, d M Y H:i:s T'));
				$this->httpResponse->contentType('text/plain');
				$this->httpResponse->setResponse(new TextResponse(sprintf('The requested method [%s] is not supported; allowed methods are [%s].', $method, $allowed)));
				return null;
			}
			if (isset($methodList[$method]) === false) {
				$this->httpResponse->setCode(self::ERROR_NOT_ALOWED);
				$headerList = $this->httpResponse->getHeaderList();
				$headerList->set('Allowed', $allowed = implode(', ', array_keys($methodList)));
				$headerList->set('Date', gmdate('D, d M Y H:i:s T'));
				$this->httpResponse->contentType('text/plain');
				$this->httpResponse->setResponse(new TextResponse(sprintf('The requested method [%s] is not implemented; allowed methods are [%s].', $method, $allowed)));
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
	}
