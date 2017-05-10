<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Request;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\Request\IMessage;
	use Edde\Api\Protocol\Request\IRequest;
	use Edde\Api\Protocol\Request\IResponse;

	/**
	 * Request handler connected to container.
	 */
	class ContainerRequestHandler extends AbstractRequestHandler {
		use LazyContainerTrait;

		/**
		 * @inheritdoc
		 *
		 * @param IMessage|IRequest $element
		 */
		public function canHandle(IElement $element): bool {
			if (parent::canHandle($element) === false) {
				return false;
			}
			return strpos($element->getRequest(), '::') !== false;
		}

		/**
		 * @inheritdoc
		 *
		 * @param IMessage|IRequest $element
		 */
		public function execute(IElement $element) {
			if (strpos($request = $element->getRequest(), '::') === false) {
				return null;
			}
			list($name, $method) = explode('::', $request);
			$class = $this->container->create($name);
			$response = $class->{$method}($element);
			if ($element instanceof IRequest && $response instanceof IResponse === false) {
				throw new MissingResponseException(sprintf('Missing response for request [%s].', $request));
			}
			return $response;
		}
	}
