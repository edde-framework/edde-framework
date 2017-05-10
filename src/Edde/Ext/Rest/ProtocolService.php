<?php
	declare(strict_types=1);

	namespace Edde\Ext\Rest;

	use Edde\Api\Protocol\IPacket;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Protocol\Error;
	use Edde\Common\Rest\AbstractService;
	use Edde\Common\Strings\StringUtils;
	use Edde\Ext\Protocol\PacketResponse;

	class ProtocolService extends AbstractService {
		use LazyProtocolServiceTrait;
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

		protected function packetExecute(IPacket $packet = null) {
			return new PacketResponse($this->protocolService->execute($packet));
		}

		protected function packetQueue(IPacket $packet = null) {
			return $this->packetExecute($packet->async());
		}

		protected function packet(string $action, array $allowed, bool $packet = false, string $id = null) {
			if (in_array($action, $allowed) === false) {
				$packet = $this->protocolService->createPacket();
				$packet->addElement(new Error(0, sprintf('The action [%s] is not supported in the given context; try [%s] or another HTTP method.', $action, implode(', ', $allowed))));
				return new PacketResponse($packet);
			}
			if (method_exists($this, $method = sprintf('packet%s', StringUtils::firstUpper($action)))) {
				return $this->$method($packet = $packet ? $this->request->getContent([IPacket::class]) : null);
			}
			$packet = $this->protocolService->createPacket();
			$packet->addElement(new Error(0, sprintf('Calling unknown action [%s]; allowed are [%s].', $action, implode(', ', $allowed))));
			return new PacketResponse($packet);
		}

		public function restGet() {
			return $this->packet($this->action, ['reference'], false, $this->id);
		}

		public function restPost() {
			return $this->packet($this->action, [
				'execute',
				'queue',
			], true, $this->id);
		}
	}
