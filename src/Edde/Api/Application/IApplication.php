<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	/**
	 * Application should connect services for user input translation to
	 * a response, for example router service to protocol service.
	 */
	interface IApplication {
		/**
		 * set an exit code from an application
		 *
		 * @param int $code
		 *
		 * @return IApplication
		 */
		public function setCode(int $code): IApplication;

		public function run(): int;
	}
