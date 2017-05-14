<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Request;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\Request\IRequestHandler;
	use Edde\Common\Protocol\AbstractProtocolHandler;

	abstract class AbstractRequestHandler extends AbstractProtocolHandler implements IRequestHandler {
		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			return in_array($element->getType(), [
				'request',
				'message',
			]);
		}
	}
