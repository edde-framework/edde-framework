<?php
	declare(strict_types = 1);

	namespace Edde\Api\Router;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Container\IConfigurable;

	interface IRouter extends IConfigurable {
		/**
		 * can this router handle current request (can be arbitrary, e.g. cli run)
		 *
		 * @return IRequest|null
		 */
		public function createRequest();
	}
