<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Job\LazyJobManagerTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IProtocolHandler;
	use Edde\Api\Protocol\LazyElementStoreTrait;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractProtocolHandler extends Object implements IProtocolHandler {
		use LazyElementStoreTrait;
		use LazyJobManagerTrait;
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
		public function execute(IElement $element) {
			try {
				if ($element->isAsync()) {
					$this->jobManager->queue($element->async(false));
					return $this->onQueue($element);
				}
				return $response = $this->onExecute($element);
			} finally {
				$this->elementStore->save($element);
				/** @var $response IElement */
				if (isset($response) && $response instanceof IElement) {
					$this->elementStore->save($response);
				}
			}
		}

		protected function onExecute(IElement $element) {
		}

		protected function onQueue(IElement $element) {
		}
	}
