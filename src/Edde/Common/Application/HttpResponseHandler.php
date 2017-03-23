<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IResponse;
	use Edde\Api\Application\IResponseHandler;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Http\LazyHttpRequestTrait;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Common\Converter\Content;

	class HttpResponseHandler extends AbstractResponseHandler {
		use LazyHttpRequestTrait;
		use LazyHttpResponseTrait;
		use LazyConverterManagerTrait;

		/**
		 * @inheritdoc
		 */
		public function send(IResponse $response): IResponseHandler {
			$targetList = ($targetList = $response->getTargetList()) ? $targetList : $this->httpRequest->getHeaderList()
				->getAcceptList();
			$this->converterManager->setup();
			$convertable = $this->converterManager->content($response, $targetList);
			$this->httpResponse->setContent(new Content($convertable->convert(), $convertable->getTarget()));
			$this->httpResponse->send();
			return $this;
		}
	}
