<?php
	declare(strict_types=1);

	namespace Edde\Ext\Rest;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Api\Session\LazyFingerprintTrait;
	use Edde\Api\Store\LazyStoreManagerTrait;
	use Edde\Api\Thread\LazyThreadManagerTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Rest\AbstractService;
	use Edde\Ext\Protocol\ElementContent;

	class ProtocolService extends AbstractService {
		use LazyStoreManagerTrait;
		use LazyProtocolServiceTrait;
		use LazyThreadManagerTrait;
		use LazyContainerTrait;
		use LazyFingerprintTrait;

		/**
		 * @inheritdoc
		 */
		public function match(IUrl $url): bool {
			return $url->match('~^/api/v1/protocol$~') !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function link($generate, array $parameterList = []) {
			return parent::link('/api/v1/protocol', $parameterList);
		}

		public function actionPost(IElement $element) {
			$response = new ElementContent($this->protocolService->execute($this->getContent($element, IElement::class)));
			$this->threadManager->execute();
			$this->response($response);
			return $response;
		}
	}
