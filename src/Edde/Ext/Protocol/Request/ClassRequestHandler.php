<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol\Request;

	use Edde\Api\Application\LazyContextTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Common\Protocol\Request\AbstractRequestHandler;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Handles namespaced, dotted paths with slash action
	 */
	class ClassRequestHandler extends AbstractRequestHandler {
		use LazyContextTrait;
		static protected $preg = '~(?<class>[.a-z0-9-]+)/(?<action>[a-z0-9-]+)~';

		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			if (parent::canHandle($element) === false) {
				return false;
			}
			return StringUtils::match((string)$element->getAttribute('request'), self::$preg) !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element) {
			$request = StringUtils::match((string)$element->getAttribute('request'), self::$preg, true);
			/**
			 * so what about some nice and readable code ;)?
			 */
			return $this->container->create(str_replace([
				' ',
				'-',
			], [
				'\\',
				'',
			], StringUtils::capitalize(str_replace('.', ' ', $request['class']))), [], static::class)->{StringUtils::toCamelHump($request['action'])}($element);
		}
	}
