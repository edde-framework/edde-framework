<?php
	declare(strict_types=1);

	namespace Edde\Ext\Rest;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Protocol\IError;
	use Edde\Api\Protocol\IPacket;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Rest\AbstractService;
	use Edde\Common\Strings\StringUtils;
	use Edde\Ext\Protocol\PacketResponse;

	class ProtocolService extends AbstractService {
		use LazyProtocolServiceTrait;
		use LazyContainerTrait;
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

		protected function packetPacket(IPacket $packet = null) {
			$this->protocolService->setup();
			return new PacketResponse($this->protocolService->execute($packet));
		}

		protected function packet(string $action, array $allowed, bool $packet = false, string $id = null) {
			if (in_array($action, $allowed) === false) {
				/** @var $packet IPacket */
				$packet = $this->container->create(IPacket::class);
				/** @var $error IError */
				$packet->addElement($error = $this->container->create(IError::class));
				$error->setCode(0);
				$error->setMessage(sprintf('The action [%s] is not supported in the given context; try [%s] or another HTTP method.', $action, implode(', ', $allowed)));
				return new PacketResponse($packet);
			}
			if (method_exists($this, $method = sprintf('packet%s', StringUtils::firstUpper($action)))) {
				return $this->$method($packet = $packet ? $this->request->getContent([IPacket::class]) : null);
			}
			/** @var $packet IPacket */
			$packet = $this->container->create(IPacket::class);
			/** @var $error IError */
			$packet->addElement($error = $this->container->create(IError::class));
			$error->setCode(0);
			$error->setMessage(sprintf('Calling unknown action [%s]; allowed are [%s].', $action, implode(', ', $allowed)));
			return new PacketResponse($packet);
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
