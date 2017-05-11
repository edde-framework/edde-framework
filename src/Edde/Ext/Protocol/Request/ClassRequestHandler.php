<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol\Request;

	use Edde\Api\Protocol\IElement;
	use Edde\Common\Protocol\Request\AbstractRequestHandler;

	/**
	 * Handles namespaced, dotted paths with slash action
	 */
	class ClassRequestHandler extends AbstractRequestHandler {
		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element) {
		}
	}
