<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol\Request;

	use Edde\Api\Protocol\IElement;
	use Edde\Common\Protocol\Request\AbstractRequestHandler;
	use Edde\Common\Strings\StringUtils;

	class InstanceRequestHandler extends AbstractRequestHandler {
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
			$element->setMeta('::method', $method = StringUtils::toCamelHump($match['action']));
			return method_exists($class, $method);
		}
	}
