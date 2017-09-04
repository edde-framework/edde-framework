<?php
	declare(strict_types=1);

	namespace Edde\Api\Router;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Protocol\IElement;

	/**
	 * This service is responsible for user to application request translation; because
	 * whole application is build around "The Protocol", result should be packet to be
	 * executed by protocol service.
	 */
	interface IRouterService extends IConfigurable {
		/**
		 * create an (request) element; a type of element could be arbitrary valid
		 * type of protocol element
		 *
		 * @return IElement
		 */
		public function createElement(): IElement;
	}
