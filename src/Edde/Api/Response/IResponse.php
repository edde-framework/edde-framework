<?php
	declare(strict_types = 1);

	namespace Edde\Api\Response;

	/**
	 * General response from an application; this should execute any output or sending of something (for example sending http headers and body, ...).
	 */
	interface IResponse {
		/**
		 * response a repsonse to the output (http, console, ...)
		 *
		 * @return mixed
		 */
		public function send();
	}
