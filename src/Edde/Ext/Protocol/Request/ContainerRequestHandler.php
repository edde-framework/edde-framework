<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol\Request;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Common\Protocol\Request\AbstractRequestHandler;
	use Edde\Common\Protocol\Request\MissingResponseException;

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
			if (strpos($request = $element->getAttribute('request'), '::') === false) {
				return null;
			}
			list($name, $method) = explode('::', $request);
			$class = $this->container->create($name);
			/** @var $response IElement */
			$response = $class->{$method}($element);
			if (($element->getType() === 'request') && ($response === null || $response->getType() !== 'response')) {
				throw new MissingResponseException(sprintf('Missing response for request [%s].', $request));
			}
			return $response;
		}
	}
