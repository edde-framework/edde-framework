<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol\Request;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Common\Protocol\Request\AbstractRequestHandler;

	/**
	 * Request handler connected to container.
	 */
	class ContainerRequestHandler extends AbstractRequestHandler {
		use LazyContainerTrait;

		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			if (parent::canHandle($element) === false) {
				return false;
			}
			return strpos($element->getAttribute('request'), '::') !== false;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element) {
			list($name, $method) = explode('::', $element->getAttribute('request'));
			return $this->container->create($name, [], static::class)->{$method}($element);
		}
	}
