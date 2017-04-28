<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IProtocolHandler;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractProtocolHandler extends Object implements IProtocolHandler {
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
		public function dequeue(): IProtocolHandler {
			foreach ($this->queueList as $element) {
				$this->execute($element);
			}
			return $this;
		}

		abstract protected function element(IElement $element);
	}
