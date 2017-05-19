<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	use Edde\Api\Protocol\IElement;

	/**
	 * Response manager holds current Response (to keep responses immutable).
	 */
	interface IResponseManager extends IResponseHandler {
		/**
		 * who will take care about response when execute() is called? If null is provided, response manager itself will hold
		 * basic output
		 *
		 * @param IResponseHandler $responseHandler
		 *
		 * @return IResponseManager
		 */
		public function setResponseHandler(IResponseHandler $responseHandler = null): IResponseManager;

		/**
		 * is there already some response?
		 *
		 * @return bool
		 */
		public function hasResponse(): bool;

		/**
		 * set the current response
		 *
		 * @param IElement $element
		 *
		 * @return IResponseManager
		 */
		public function response(IElement $element): IResponseManager;

		/**
		 * execute response
		 *
		 * @param IElement|null $element
		 *
		 * @return mixed
		 */
		public function execute(IElement $element = null);
	}
