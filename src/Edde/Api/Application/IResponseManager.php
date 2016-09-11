<?php
	declare(strict_types = 1);

	namespace Edde\Api\Application;

	use Edde\Api\Usable\IUsable;

	/**
	 * Response manager holds current Response (to keep responses immutable).
	 */
	interface IResponseManager extends IUsable {
		/**
		 * set the current response
		 *
		 * @param IResponse $response
		 *
		 * @return IResponseManager
		 */
		public function response(IResponse $response): IResponseManager;

		/**
		 * return current response or create a default one (which will became "current")
		 *
		 * @return IResponse
		 */
		public function getResponse(): IResponse;
	}
