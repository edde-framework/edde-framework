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

		public function handle(string $method, array $parameterList, array $crateList) {
			if (isset(self::$methodList[$method = strtoupper($method)]) === false) {
				$this->httpResponse->setCode(self::ERROR_NOT_ALOWED);
				$headerList = $this->httpResponse->getHeaderList();
				$headerList->set('Allowed', implode(', ', $this->getMethodList()));
				$this->httpResponse->contentType('text/plain');
				$this->httpResponse->setResponse(new TextResponse(sprintf('The requested method [%s] is not supported.', $method)));
				return;
			}
		}

		/**
		 * return list of supported methods
		 *
		 * @return array
		 */
		public function getMethodList(): array {
			$methodList = [];
			foreach (self::$methodList as $method) {
				if (method_exists($this, 'rest' . StringUtils::firstUpper(strtolower($method)))) {
					$methodList[] = $method;
				}
			}
			return $methodList;
		}
	}
