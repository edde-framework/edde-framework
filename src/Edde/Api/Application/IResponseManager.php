<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Response manager holds current Response (to keep responses immutable).
	 */
	interface IResponseManager extends IConfigurable {
		/**
		 * set the current response
		 *
		 * @param IResponse $response
		 *
		 * @return IResponseManager
		 */
		public function response(IResponse $response = null): IResponseManager;

		/**
		 * execute response
		 *
		 * @return mixed
		 */
		public function execute();
	}
