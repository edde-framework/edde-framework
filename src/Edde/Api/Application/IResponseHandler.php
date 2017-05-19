<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Protocol\IElement;

	interface IResponseHandler extends IConfigurable {
		/**
		 * execute the response
		 *
		 * @param IElement $element
		 *
		 * @return IResponseHandler
		 */
		public function send(IElement $element): IResponseHandler;
	}
