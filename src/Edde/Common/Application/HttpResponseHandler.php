<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IResponseHandler;
	use Edde\Api\Converter\IContent;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Http\LazyHttpRequestTrait;
	use Edde\Api\Http\LazyHttpResponseTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Common\Converter\Content;

	class HttpResponseHandler extends AbstractResponseHandler {
		use LazyHttpRequestTrait;
		use LazyHttpResponseTrait;
		use LazyConverterManagerTrait;

		/**
		 * @inheritdoc
		 */
		public function send(IElement $element): IResponseHandler {
			throw new \Exception('not implemented yet: ' . __METHOD__);
			$targetList = ($targetList = $element->getTargetList()) ? $targetList : $this->httpRequest->getHeaderList()->getAcceptList();
			$this->converterManager->setup();
			$convertable = $this->converterManager->content($element, $targetList);
			$this->httpResponse->setContent((($content = $convertable->convert()) instanceof IContent) ? $content : new Content($convertable->convert(), $convertable->getTarget()));
			$this->httpResponse->send();
			return $this;
		}
	}
