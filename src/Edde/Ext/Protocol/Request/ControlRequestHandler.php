<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol\Request;

	use Edde\Api\Protocol\IElement;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Handles namespaced, dotted paths with slash action
	 */
	class ControlRequestHandler extends \Edde\Common\Request\AbstractRequestHandler {
		const PREG = '~(?<class>[\\\a-zA-Z0-9-]+)::(?<action>[a-zA-Z0-9-]+)~';

		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			if (parent::canHandle($element) === false || ($match = StringUtils::match((string)$element->getAttribute('request'), self::PREG)) === null) {
				return false;
			}
			$element->setMeta('::class', $class = str_replace([
				' ',
				'-',
			], [
				'\\',
				'',
			], StringUtils::capitalize(str_replace('.', ' ', $match['class']))));
			$element->setMeta('::method', $match['action']);
			return class_exists($class);
		}
	}
