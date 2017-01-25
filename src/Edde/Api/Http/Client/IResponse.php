<?php
	declare(strict_types=1);

	namespace Edde\Api\Http\Client;

	use Edde\Api\Http\IResponse as IHttpResponse;

	interface IResponse extends IHttpResponse {
		/**
		 * convert the reponse to something in the target list
		 *
		 * @param array $targetList
		 *
		 * @return mixed
		 */
		public function convert(array $targetList);
	}
