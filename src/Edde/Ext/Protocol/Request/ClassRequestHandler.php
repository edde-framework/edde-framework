<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol\Request;

	use Edde\Api\Protocol\IElement;
	use Edde\Common\Protocol\Request\AbstractRequestHandler;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Handles namespaced, dotted paths with slash action
	 */
	class ClassRequestHandler extends AbstractRequestHandler {
		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			if (parent::canHandle($element) === false) {
				return false;
			}
			return StringUtils::match((string)$element->getAttribute('request'), '~(\.[a-z0-9-]+)+/[a-z0-9-]+~') !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element) {
		}
	}
