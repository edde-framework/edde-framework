<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	use Edde\Api\Config\IConfigurable;

	interface IResponseHandler extends IConfigurable {
		/**
		 * execute the response
		 *
		 * @param IResponse $response
		 *
		 * @return IResponseHandler
		 */
		public function send(IResponse $response): IResponseHandler;
	}
