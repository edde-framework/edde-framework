<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IProtocolHandler;
	use Edde\Api\Protocol\LazyElementStoreTrait;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractProtocolHandler extends Object implements IProtocolHandler {
		use LazyElementStoreTrait;
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
				return $response = $this->onExecute($element);
			} finally {
				$this->elementStore->save($element);
				/** @var $response IElement */
				if ($response instanceof IElement) {
					$this->elementStore->save($response);
				}
			}
		}

		protected function onExecute(IElement $element) {
		}
	}
