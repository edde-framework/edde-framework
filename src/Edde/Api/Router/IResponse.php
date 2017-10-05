<?php
	declare(strict_types=1);
	namespace Edde\Api\Router;

	interface IResponse {
		/**
		 * execute an application response (this should echo the things, do computations)
		 *
		 * @return int
		 */
		public function execute(): int;
	}
