<?php
	declare(strict_types=1);

	namespace Edde\Ext\Rest;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Http\LazyHostUrlTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\LazyElementQueueTrait;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Api\Session\LazyFingerprintTrait;
	use Edde\Api\Thread\LazyThreadManagerTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Protocol\Packet;
	use Edde\Common\Rest\AbstractService;
	use Edde\Ext\Protocol\ElementContent;

	class ProtocolService extends AbstractService {
		use LazyProtocolServiceTrait;
		use LazyElementQueueTrait;
		use LazyThreadManagerTrait;
		use LazyContainerTrait;
		use LazyHostUrlTrait;
		use LazyFingerprintTrait;

		/**
		 * @inheritdoc
		 */
		public function match(IUrl $url): bool {
			if (($match = $url->match('~^/api/v1/protocol$~')) === null) {
				return false;
			}
			return true;
		}

		/**
		 * @inheritdoc
		 */
		public function link($generate, array $parameterList = []) {
			return parent::link('/api/v1/protocol', $parameterList);
		}

		public function actionGet(IElement $element) {
			$this->elementQueue->load();
			if (($reference = $element->getMeta('reference')) !== null) {
				$response = new ElementContent((new Packet($this->hostUrl->getAbsoluteUrl()))->elements($this->elementQueue->getReferenceList((string)$reference)));
			} else {
				$response = new ElementContent($this->protocolService->createQueuePacket((string)$element->getMeta('scope'), ['session' => $this->fingerprint->fingerprint()]));
			}
			$this->response($response);
			return $response;
		}

		public function actionPost(IElement $element) {
			$response = new ElementContent($this->protocolService->element($this->getContent($element, IElement::class)));
			$this->elementQueue->save();
			if ($this->elementQueue->isEmpty() === false) {
				$this->threadManager->execute();
			}
			$this->response($response);
			return $response;
		}
	}
