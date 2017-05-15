<?php
	declare(strict_types=1);

	namespace Edde\Ext\Rest;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Http\LazyHostUrlTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Protocol\Error;
	use Edde\Common\Protocol\Packet;
	use Edde\Common\Rest\AbstractService;
	use Edde\Common\Strings\StringUtils;
	use Edde\Ext\Protocol\ElementResponse;

	class ProtocolService extends AbstractService {
		use LazyProtocolServiceTrait;
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
			return new ElementResponse($this->protocolService->execute($element));
		}

		protected function packet(string $action, array $allowed, bool $packet = false, string $id = null) {
			if (in_array($action, $allowed) === false) {
				$packet = new Packet($this->hostUrl->getAbsoluteUrl());
				$packet->addElement('elements', $error = new Error(0, sprintf('The action [%s] is not supported in the given context; try [%s] or another HTTP method.', $action, implode(', ', $allowed))));
				return new ElementResponse($packet);
			}
			if (method_exists($this, $method = sprintf('packet%s', StringUtils::firstUpper($action)))) {
				return $this->$method($packet = $packet ? $this->request->getContent([IElement::class]) : null);
			}
			$packet = new Packet($this->hostUrl->getAbsoluteUrl());
			$packet->addElement('elements', $error = new Error(0, sprintf('Calling unknown action [%s]; allowed are [%s].', $action, implode(', ', $allowed))));
			return new ElementResponse($packet);
		}

		public function restGet() {
			return $this->packet($this->action, [], false, $this->id);
		}

		public function restPost() {
			return $this->packet($this->action, [
				'packet',
			], true, $this->id);
		}
	}
