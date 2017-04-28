<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IPacket;
	use Edde\Api\Protocol\IProtocolHandler;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractProtocolHandler extends Object implements IProtocolHandler {
		use LazyContainerTrait;
		use ConfigurableTrait;
		/**
		 * @var IElement[]
		 */
		protected $queueList = [];

		/**
		 * @inheritdoc
		 */
		public function check(IElement $element): IProtocolHandler {
			if ($this->canHandle($element)) {
				return $this;
			}
			throw new UnsupportedElementException(sprintf('Unsupported element [%s (%s)] in protocol handler [%s].', $element->getType(), get_class($element), static::class));
		}

		/**
		 * @inheritdoc
		 */
		public function queue(IElement $element): IProtocolHandler {
			$this->check($element);
			$this->queueList[] = $element;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element) {
			$this->check($element);
			return $this->element($element);
		}

		/**
		 * @inheritdoc
		 */
		public function dequeue(string $scope = null, array $tagList = null): IProtocolHandler {
			foreach ($this->iterate($scope, $tagList) as $element) {
				$this->execute($element);
			}
			return $this;
		}

		/**
		 * @param string|null $scope
		 * @param array|null  $tagList
		 *
		 * @return \Generator|IElement[]
		 */
		public function iterate(string $scope = null, array $tagList = null) {
			foreach ($this->queueList as $element) {
				if ($element->inScope($scope) && $element->inTagList($tagList)) {
					yield $element;
				}
			}
		}

		/**
		 * @inheritdoc
		 */
		public function packet(string $scope = null, array $tagList = null, IPacket $packet = null): IPacket {
			/** @var IPacket $packet */
			if ($packet === null) {
				$packet = $this->container->create(IPacket::class);
				$packet->setScope($scope);
				$packet->setTagList($tagList);
			}
			foreach ($this->iterate($scope, $tagList) as $element) {
				$packet->addElement($element);
			}
			return $packet;
		}

		abstract protected function element(IElement $element);
	}
