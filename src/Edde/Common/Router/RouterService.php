<?php
	declare(strict_types=1);

	namespace Edde\Common\Router;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Router\IRouterService;
	use Edde\Common\Object\Object;

	class RouterService extends Object implements IRouterService {
		/**
		 * @var IElement
		 */
		protected $element;

		public function createElement(): IElement {
		}
	}
