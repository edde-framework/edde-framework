<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Http\LazyHostUrlTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IProtocolHandler;
	use Edde\Api\Protocol\LazyElementQueueTrait;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractProtocolHandler extends Object implements IProtocolHandler {
		use LazyContainerTrait;
		use LazyElementQueueTrait;
		use LazyHostUrlTrait;
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 */
		public function check(IElement $element): IProtocolHandler {
			if ($this->canHandle($element)) {
				return $this;
			}
			throw new UnsupportedElementException(sprintf('Unsupported element [%s] in protocol handler [%s].', $element->getName(), static::class));
		}

		/**
		 * @inheritdoc
		 */
		public function queue(IElement $element) {
			$this->check($element);
			$this->elementQueue->queue($element);
		}

		/**
		 * @inheritdoc
		 */
		public function element(IElement $element) {
			$this->check($element);
			if ($element->isAsync()) {
				/** @var $async IElement */
				if (($async = $this->queue($element)) instanceof IElement) {
					$async->setReference($element);
				}
				return $async;
			}
			return $this->execute($element);
		}

		/**
		 * @param IElement $element
		 *
		 * @return IElement|null
		 */
		protected function createAsyncElement(IElement $element) {
			return null;
		}
	}
