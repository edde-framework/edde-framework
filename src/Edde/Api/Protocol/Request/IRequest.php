<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol\Request;

	use Edde\Api\Protocol\IElement;

	/**
	 * Request is special kind of Message expecting some response.
	 */
	interface IRequest extends IElement {
		/**
		 * @param string $request
		 *
		 * @return IRequest
		 */
		public function setRequest(string $request): IRequest;

		/**
		 * @return string
		 */
		public function getRequest(): string;
	}
