<?php
	declare(strict_types=1);

	namespace Edde\Ext\Rest;

	use Edde\Api\Converter\IContent;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\LazyProtocolManagerTrait;
	use Edde\Api\Thread\LazyThreadManagerTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Rest\AbstractService;
	use Edde\Ext\Protocol\ElementContent;

	class ProtocolService extends AbstractService {
		use LazyProtocolManagerTrait;
		use LazyThreadManagerTrait;

		/**
		 * @inheritdoc
		 */
		public function match(IUrl $url): bool {
			return $url->match('~^/api/protocol$~') !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function link($generate, array $parameterList = []) {
			return parent::link('/api/protocol', $parameterList);
		}

		/**
		 * simple request to get current packet
		 *
		 * @param IElement $element
		 *
		 * @return IContent
		 */
		public function actionGet(IElement $element) {
			return $this->response(new ElementContent($this->protocolManager->createPacket()));
		}

		/**
		 * post packet which will be executed
		 *
		 * @param IElement $element
		 *
		 * @return IContent
		 */
		public function actionPost(IElement $element) {
			$this->response($response = new ElementContent($this->protocolManager->execute($this->getContent($element, IElement::class))));
			$this->threadManager->execute();
			return $response;
		}
	}
