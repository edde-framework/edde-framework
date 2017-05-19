<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	use Edde\Api\Protocol\IElement;

	/**
	 * Single application implementation; per project should be exactly one instance (implementation) of this interface.
	 */
	interface IApplication {
		/**
		 * execute main "loop" of application (process the given request)
		 *
		 * @return mixed
		 */
		public function run();

		/**
		 * execute the given application request
		 *
		 * @param IElement $element
		 *
		 * @return IElement
		 */
		public function execute(IElement $element): IElement;
	}
