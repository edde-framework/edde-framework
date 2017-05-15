<?php
	declare(strict_types=1);

	namespace Edde\Ext\Rest;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Http\LazyHostUrlTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\LazyElementQueueTrait;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Api\Thread\LazyThreadManagerTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Protocol\Error;
	use Edde\Common\Protocol\Packet;
	use Edde\Common\Rest\AbstractService;
	use Edde\Common\Strings\StringUtils;
	use Edde\Ext\Protocol\ElementResponse;

	class ProtocolService extends AbstractService {
		use LazyProtocolServiceTrait;
		use LazyElementQueueTrait;
		use LazyThreadManagerTrait;
		use LazyContainerTrait;
		use LazyHostUrlTrait;
		protected $action;
		protected $id;

		/**
		 * @inheritdoc
		 */
		public function match(IUrl $url): bool {
			if (($match = $url->match('~^/api/v1/protocol(/(?<action>.+?)(/(?<id>.+?))?)?$~')) === null) {
				return false;
			}
			$this->action = $match['action'] ?? null;
			$this->id = $match['id'] ?? null;
			return true;
		}

		/**
		 * @inheritdoc
		 */
		public function link($generate, ...$parameterList) {
			return parent::link('/api/v1/protocol', ...$parameterList);
		}

		protected function packetPacket(IElement $element = null) {
			$response = new ElementResponse($this->protocolService->element($element));
			$this->elementQueue->save();
			if ($this->elementQueue->isEmpty() === false) {
				$this->threadManager->execute();
			}
			return $response;
		}

		protected function packet(string $action, array $allowed, bool $packet = false, string $id = null) {
			$response = new ElementResponse((new Packet($this->hostUrl->getAbsoluteUrl()))->element(new Error(0, sprintf('Calling unknown action [%s]; allowed are [%s].', $action, implode(', ', $allowed)))));
			if (in_array($action, $allowed) === false) {
				$response = new ElementResponse((new Packet($this->hostUrl->getAbsoluteUrl()))->element(new Error(0, sprintf('The action [%s] is not supported in the given context; try [%s] or another HTTP method.', $action, implode(', ', $allowed)))));
			}
			if (method_exists($this, $method = sprintf('packet%s', StringUtils::firstUpper($action)))) {
				$response = $this->$method($packet = $packet ? $this->request->getContent([IElement::class]) : null);
			}
			return $response;
		}

		public function restGet() {
			$this->elementQueue->load();
			$packet = new Packet($this->hostUrl->getAbsoluteUrl());
			$packet->setElementList('elements', $this->elementQueue->getReferenceList($this->id));
			return new ElementResponse($packet);
		}

		public function restPost() {
			return $this->packet($this->action, [
				'packet',
			], true, $this->id);
		}
	}
